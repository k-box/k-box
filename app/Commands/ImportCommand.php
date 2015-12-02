<?php namespace KlinkDMS\Commands;

use Illuminate\Console\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use KlinkDMS\Import;
use KlinkDMS\User;
use KlinkDMS\File;
use KlinkDMS\Group;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Support\Facades\Log;
use Barryvdh\Queue\Jobs\AsyncJob;
use \Klink\DmsDocuments\DocumentsService;
use Illuminate\Support\Facades\File as Storage;

class ImportCommand extends Command implements ShouldBeQueued, SelfHandling {

	use InteractsWithQueue, SerializesModels;
        
    const CHUNKSIZE = 2048; //chunk size
        
        private $user;
        private $url;
        private $import;
    
    private $service = null;

    /**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(\Klink\DmsDocuments\DocumentsService $documentsService)
	{
        $this->service = $documentsService;
	}
        
    /**
	 * Execute the command of the queue.
	 *
	 * @return void
	 */
	public function init( AsyncJob $job,$array)
	{
        set_time_limit(0);

        Log::info('Async Job', array('context' => 'ImportCommand@init', 'job' => $job, 'arguments' => $array));

        $import = $array['import'];
        $user = $array['user'];
        $group = isset($array['group']) ? Group::findOrFail($array['group']) : null;
        
        try {

            $visibility = isset($array['visibility']) && !empty($array['visibility']) ? \KlinkVisibilityType::fromString($array['visibility']) : 'private';

        }catch(\InvalidArgumentException $ie) {
            
            Log::error('Wrong visibility in arguments', array('context' => 'ImportCommand@init', 'arguments' => $array));

            $visibility = 'private'; //default back to private
        }
        
        $this->user = $user;
        
        $this->import = $import;
        

        $good = false;

        if($import->is_remote){

            $this->url = $import->file->original_uri;

            // remote file based import

            // 1. download the file (the File entry is already in the db)
            $good = $this->downloadFile($this->url,false);

            // 2. create a document descriptor and start indexing

            if($good){
                

                $file = $import->file->fresh();
                
                $extracted_title = $this->service->guessTitleFromFile($file);
                
                if(!empty($extracted_title) && $file->name !== $extracted_title){
                    $file->name = $extracted_title;
                    $file->save();
                    $file = $file->fresh();
                }

                try{

                    $descriptor = $this->service->indexDocument( $file, $visibility, null, $group);

                    // if(!is_null($group)){
                    //     $descriptor->groups()->save($group);
                    // }

                } catch(\KlinkException $kex){
                    Log::error('Indexing during import exception', array('context' => 'ImportCommand@init', 'import' => $import->toArray(), 'import_file' => $file->toArray(), 'is_remote' => true));
                }catch(\InvalidArgumentException $kex){
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('Indexing during import exception', array('context' => 'ImportCommand@init', 'import' => $import->toArray(), 'import_file' => $file->toArray(), 'is_remote' => true));
                } catch(\Exception $kex){
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('Indexing during import exception', array('context' => 'ImportCommand@init', 'import' => $import->toArray(), 'import_file' => $file->toArray(), 'is_remote' => true));
                }

            }
            else {
                Log::error('File download returned error', array('context' => 'ImportCommand@init', 'import' => $import->toArray(), 'is_remote' => true));
            }

        }else{
            
            // folder based import

            $good = $this->importFolder($this->import, $visibility, $group);

        }
        
        if($good){
            $job->delete();    
        }

	}



    private function importFolder(Import $import, $visibility = 'private', Group $group = null){

        // 1. scan the folder for files

        $folder = $import->file;

        $files = Storage::files(str_replace('\\', '/', $folder->original_uri));

        Log::info('Import folder', array('context' => 'ImportCommand@importFolder', 'files' => $files));

        // 2. update the bytes_expected to the number of files in that folder

        $import->bytes_expected = count($files);

        $import->status = Import::STATUS_DOWNLOADING;
        $import->status_message = Import::MESSAGE_DOWNLOADING;

        $import->save();

        $file_model = null;

        // 3. start copying all the files

        $count = 0;
        $descriptor = null;

        $supported_extensions = array_filter(explode(',', \Config::get('dms.allowed_file_types') ));

        foreach ($files as $file) {
            
            $filename = basename($file);

            $destination_path = $folder->path . '/' . $filename;

            $file_m_time = @filemtime($file);

            $hash_before = \KlinkDocumentUtils::generateDocumentHash($file);


            $extension = pathinfo( $file, PATHINFO_EXTENSION );

            if(in_array($extension, $supported_extensions)){

                $dirname = str_replace('\\', '/', dirname($destination_path));
                $same_folder = starts_with($file, $dirname);
                
                $copied = $same_folder ? true : copy($file, $destination_path);

                $hash_after = \KlinkDocumentUtils::generateDocumentHash($destination_path);

                $file_already_exists = File::existsByHashAndSourceFolder($hash_after, $file);


                if($copied && $hash_before == $hash_after && !$file_already_exists){

                    $mime = \KlinkDocumentUtils::get_mime($destination_path);

                    // 4. create a File entry for each file found

                    $file_model = new File();
                    $file_model->name= $filename;
                    $file_model->hash=$hash_after;
                    $file_model->mime_type=$mime; 
                    $file_model->size= Storage::size($destination_path);
                    $file_model->thumbnail_path=null;
                    $file_model->path = $destination_path;
                    $file_model->user_id = $import->user_id;
                    $file_model->original_uri = $file;
                    $file_model->is_folder = false;
                    
                    if(!$file_m_time){
                        $file_model->created_at = \Carbon\Carbon::createFromFormat('U', $file_m_time);
                    }
                    $file_model->save();

                    Log::info('Import file ' . $file, array('context' => 'ImportCommand@importFolder', 'file_model' => $file_model));

                    try{

                        $descriptor = $this->service->indexDocument( $file_model, $visibility, null, $group );

                        // if(!is_null($group)){
                        //     $descriptor->groups()->save($group);
                        // }

                    } catch(\InvalidArgumentException $kex){
                        // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                        // at a later time
                        Log::error('Indexing during import exception', array('context' => 'ImportCommand@init', 'exception' => $kex, 'import' => $import->toArray(), 'import_file' => $file, 'is_remote' => false));
                    } catch(\KlinkException $kex){
                        // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                        // at a later time
                        Log::error('Indexing during import exception', array('context' => 'ImportCommand@init', 'exception' => $kex, 'import' => $import->toArray(), 'import_file' => $file, 'is_remote' => false));
                    } catch(\Exception $kex){
                        // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                        // at a later time
                        Log::error('Indexing during import exception', array('context' => 'ImportCommand@init', 'exception' => $kex, 'import' => $import->toArray(), 'import_file' => $file, 'is_remote' => false));
                    }

                    $count++;
                    $import->bytes_received = $count;
                    $import->save();

                }
                else {


                    if($file_already_exists){

                        Log::warning('Skipping file import - already exists -', array('context' => 'ImportCommand@init', 'import' => $import->toArray(), 'import_file' => $folder->toArray(), 'file' => $file));

                        $count++;
                        $import->bytes_received = $count;
                        $import->save();
                        
                    }
                    else {

                        Log::error('File copy error', array('context' => 'ImportCommand@init', 'import' => $import->toArray(), 'import_file' => $folder->toArray(), 'file' => $file));

                        $import->status = Import::STATUS_ERROR;
                        $import->status_message = Import::MESSAGE_ERROR;

                        $import->save();

                        return false;
                    }
                }

            }
            else {

                Log::warning('Skipping file import - Type not supported -', array('context' => 'ImportCommand@init', 'import' => $import->toArray(), 'import_file' => $folder->toArray(), 'file' => $file));

                $count++;
                $import->bytes_received = $count;
                $import->save();

            }

        }


        $import->status = Import::STATUS_COMPLETED;
        $import->status_message = Import::MESSAGE_COMPLETED;

        $import->save();        
        
        // 5. create folder groups (how?)


        return true;
    }











        /*
         * starting point
         */
        function downloadShared($root){
            //if it's a directory, just edit the infos
            $import = $this->import;
            $file = File::find($import->file_id);
            $this->file = $file;
            if(is_dir($root)){
                $file_info = $this->get_file_info('file://'.str_replace("\\", '/', $file->original_uri));
                $file->mime_type = 'folder';
                $file->size = 1;
                $file->name = $file_info['name'];
                $file->path = \Config::get('dms.upload_folder')."".$file->id;//for folders, just to make it unique
                $file->update();
                
                $import->status = Import::STATUS_COMPLETED;
                $import->status_message = Import::MESSAGE_COMPLETED;
                $import->bytes_expected = 1;
                $import->bytes_received = 1;
                $import->update();
            }else{//it's not a folder, so treat like a normal remote url
                //not working.
                $url = 'file://'.str_replace("\\", '/', $file->original_uri);
                $this->downloadFile($url,true);
            }
        }


    // (below) Made by others, seems to work so I don't care ----------------------------

        
        /*
        Set Headers
        Get total size of file
        Then loop through the total size incrementing a chunck size
        */
        function downloadFile($url,$local_request){
            /*
             * get the remote file headers
             */
            set_time_limit(0);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-disposition: attachment; filename='.basename($url));
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Expires: 0');
            header('Pragma: public');
            $file_info = $this->get_file_info($url);
            header('Content-Length: '.$file_info['size']);
            $import = $this->import;
            $file = File::find($import->file_id);
            if(!$local_request){
                $file->mime_type =$file_info['mimetype'];
            }else if(mime_content_type($url)!=null){
                $file->mime_type= mime_content_type($url);
            }else{
                $file->mime_type= 'undefined';
            }
                        
            $file->size = $file_info['size'] < 0 ? 0 : $file_info['size'];

            if(isset($file_info['name']) && !empty($file_info['name'])){
                $file->name = $file_info['name'];
            }
            
            $ext= $file->mime_type == "undefined" ? 
                    explode('.',$file->original_uri)[count(explode('.',$file->original_uri)-1)] : 
                    \KlinkDocumentUtils::isMimeTypeSupported($file->mime_type) ? 
                        \KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type)
                        : explode('.',$file->original_uri)[count(explode('.',$file->original_uri))-1]; //get the extension from the original url if ext not found
            
            // $file->path = \Config::get('dms.upload_folder').$file->id.".".  $ext; // already calculated before adding to the queue
            
            $file->update();
            $this->file = $file;
            $import->bytes_expected = $file->size;
            $import->bytes_received = 0;
            $import->status = Import::STATUS_QUEUED;
            $import->status_message = Import::MESSAGE_QUEUED;
            $import->update();

            $i = 0;
            $size = $file_info['size']; //Size is -1 on text/html

            $good = true;

            if($size < 0){

                try{

                    file_put_contents($file->path, file_get_contents($url));

                    $import->status = Import::STATUS_COMPLETED;
                    $import->status_message = Import::MESSAGE_COMPLETED;
                    
                    $import->update();
                    $file = File::find($file->id);
                    $file->hash = \KlinkDocumentUtils::generateDocumentHash($file->path); //file downloaded and saved
                    $file->update();

                }catch(\Exception $ex){

                    $import->status = Import::STATUS_ERROR;
                    $import->status_message = Import::MESSAGE_ERROR_LOOSE_CHUNKS;
                    $import->update();

                    $good = false;

                }

            }
            else {

                while($i<=$size){
                    //get chunks in order
                    $this->get_chunk($url,(($i==0)?$i:$i+1),((($i+self::CHUNKSIZE)>$size)?$size:$i+self::CHUNKSIZE));
                    $i = ($i+self::CHUNKSIZE);
                }
                $import = Import::find($this->import->id);
                if($import->bytes_expected!=$import->bytes_received){
                    $import->status = Import::STATUS_ERROR;
                    $import->status_message = Import::MESSAGE_ERROR_LOOSE_CHUNKS;
                    $good = false;
                }else{
                    $import->status = Import::STATUS_COMPLETED;
                    $import->status_message = Import::MESSAGE_COMPLETED;
                }
                $import->update();
                $file = File::find($file->id);
                $file->hash = \KlinkDocumentUtils::generateDocumentHash($file->path); //file downloaded and saved
                $file->update();
            }

            return $good;
        }

        /**
         * Callback function for CURLOPT_WRITEFUNCTION
         */
        function chunk($ch, $str) {
            $import = Import::find($this->import->id);
            $import->status = Import::STATUS_DOWNLOADING;
            $import->status_message = Import::MESSAGE_DOWNLOADING;

            $out = fopen($this->file->path,'a+');
            
            if($out){
                fwrite($out, $str);
                $import->bytes_received+=strlen($str);
                fclose($out);
            }
            $import->update();
            
            return strlen($str);
        }

        /**
         * Function to get a range of bytes from the remote file
         */
        function get_chunk($file,$start,$end){
            
            $callback = function ($ch, $str){
                return $this->chunk($ch, $str);
            };
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $file);
            curl_setopt($ch, CURLOPT_RANGE, $start.'-'.$end);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
            $result = curl_exec($ch);
            curl_close($ch);
        }

        /**
         * Get total size of file
         */
        function get_file_info($url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $name_with_ext = explode('/', $url)[count(explode('/', $url))-1];
            $name = str_contains($name_with_ext, '.') ? explode('.',$name_with_ext)[0] : $name_with_ext;
            return array(
                'size' => intval(curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD)),
                'mimetype' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
                'name' => $name // come nome del file, l'ultima parte dell'url
            );
        }
        


}
