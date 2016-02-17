<?php namespace KlinkDMS\Jobs;

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
use \Klink\DmsDocuments\DocumentsService;
use Illuminate\Support\Facades\File as Storage;
use Illuminate\Support\Facades\Storage as StorageFacade;
use Symfony\Component\Finder\Finder;
use GuzzleHttp\Client;
use KlinkDMS\Exceptions\FileDownloadException;

class ImportCommand extends Job implements ShouldBeQueued, SelfHandling {

	use InteractsWithQueue, SerializesModels;
        
    const CHUNKSIZE = 2048; //chunk size
        
    private $user;
    private $url;
    private $import;
    private $group;
    private $copy;
    private $exclude;
    
    private $service = null;

    /**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, Import $import, Group $group = null, $copy = false, $exclude_folders = null)
	{
        $this->user = $user;
        $this->url = null;
        $this->import = $import;
        $this->copy = $copy;
        $this->group = $group;
        $this->exclude = $exclude_folders;
	}
        

    /**
     * Execute the command.
     *
     * Peforms import
     */
    public function handle(\Klink\DmsDocuments\DocumentsService $documentsService){
        
        try{
        
            Log::info('Executing ImportCommand', array(
                'import' => $this->import,'user' => $this->user,'group' => $this->group,
                'copy' => $this->copy,'exclude' => $this->exclude,
            ));
            
            $this->service = $documentsService;
                        
            if($this->import->is_remote){
                $this->doImportUrl();
            }
            else {
                $this->doImportFolder();
            }
    
            $this->import->status = Import::STATUS_COMPLETED;
            $this->import->status_message = Import::MESSAGE_COMPLETED;
    
            $this->import->save();

        } catch(\Exception $kex){
            
            Log::error('ImportCommand: unhandled Exception while importing', array('exception' => $kex, 'import' => $this->import->toArray()));
            
            $this->import->status = Import::STATUS_ERROR;
            $this->import->status_message = Import::MESSAGE_ERROR;
            $this->import->message = $kex->getMessage();
            $this->import->payload = array( 'error' => class_basename( $kex ) . ' in ' . basename( $kex->getFile() ) . ' line ' . $kex->getLine() . ': ' . $kex->getMessage() );
    
            $this->import->save();
            
            $this->fail();
            
            // throw $kex;
        }
        
    }
    
    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed()
    {
        \Log::warning('Job Failed');
    }
    
    
    function doImportUrl(){
        
        $file = $this->import->file;
        
        $this->url = $this->import->file->original_uri;

        // remote file based import

        // 1. download the file (the File entry is already in the db)
        
        // update import status to Downloading
        $this->import->status = Import::STATUS_DOWNLOADING;
        $this->import->status_message = Import::MESSAGE_DOWNLOADING;
        $this->import->save();        
        
        // really download the file
        
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->url,
            // You can set any number of default request options.
            'timeout'  => 10.0,
        ]);
        
        $response = $client->request('GET', $this->url, ['sink' => $file->path]);
        
        $response_headers = $response->getHeaders();
        
        $good = $response->getStatusCode() === 200;
        
        if($good){
            
            $content_type_header = $response_headers['Content-Type'];
            if( is_array($content_type_header) ){
                $content_type_header = join(' ', $content_type_header);
            }
            // update the File entry in the DB with mimetype, size, and stuff like that
            
            $file->mime_type = $content_type_header;
            $file->size = Storage::size($file->path);
            
            $file->hash = \KlinkDocumentUtils::generateDocumentHash($file->path);
            
            $this->import->bytes_expected = $file->size;
            $this->import->bytes_received = $file->size;
            
            // $ext= $file->mime_type == "undefined" ? 
            //             explode('.',$file->original_uri)[count(explode('.',$file->original_uri)-1)] : 
            //             \KlinkDocumentUtils::isMimeTypeSupported($file->mime_type) ? 
            //                 \KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type)
            //                 : explode('.',$file->original_uri)[count(explode('.',$file->original_uri))-1]; //get the extension from the original url if ext not found
            
            $extracted_title = $this->service->guessTitleFromFile($file);
            
            if(!empty($extracted_title) && $file->name !== $extracted_title){
                $file->name = $extracted_title;
            }
            
            $file->save();
            $file = $file->fresh();
            
            
            // 2. create a document descriptor and start indexing
            
            $this->import->status = Import::STATUS_INDEXING;
            $this->import->status_message = Import::MESSAGE_INDEXING;
            $this->import->save();


            try{

                $descriptor = $this->service->indexDocument( $file, \KlinkVisibilityType::KLINK_PRIVATE, $this->user);

            } catch(\KlinkException $kex){
                
                Log::error('ImportCommand Indexing error: KlinkException', array('exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => true));
                
            }catch(\InvalidArgumentException $kex){
                
                Log::error('ImportCommand Indexing error: InvalidArgumentException', array('exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => true));
                
            } catch(\Exception $kex){
                
                Log::error('ImportCommand Indexing error', array('exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => true));
                
            }

        }
        else {
            Log::error('File download returned error', array('context' => 'ImportCommand@init', 'import' => $this->import->toArray(), 'is_remote' => true));
            
            throw new FileDownloadException( trans('errors.import.download_error', ['url' => $this->url, 'error' => $response->getReasonPhrase()]), $file );
        }
        
    }
    
    
    function doImportFolder(){
        
        $folder = $this->import->file;
            
        $visibility = \KlinkVisibilityType::KLINK_PRIVATE;

        $files = $this->files(realpath($folder->original_uri), $this->exclude);

        $this->import->bytes_expected = count($files);

        $this->import->status = Import::STATUS_DOWNLOADING;
        $this->import->status_message = Import::MESSAGE_DOWNLOADING;

        $this->import->save();

        $file_model = null;
        $count = 0;
        $descriptor = null;
        $file = null;

        foreach ($files as $original_file) {
            
            $file = $original_file;
            
            if($this->copy){ 
                
                $file = $folder->path . DIRECTORY_SEPARATOR . basename($original_file);
                
                $copied = @copy($original_file, $file);
                
                if(!$copied){
                    $errors= error_get_last();
                    
                    Log::error('File cannot be copied', array('folder' => $folder, 'file' => $file, 'original_file' => $original_file, 'constructed_path' => $folder->path . DIRECTORY_SEPARATOR . basename($original_file)));

                    throw new \Exception('Document '. basename($original_file) .' cannot be copied ('.$original_file.' '. (isset($errors['message'])?$errors['message']:'') .')');
                }
                
            }
                
            $hash = \KlinkDocumentUtils::generateDocumentHash($file);
    
            $file_already_exists = File::existsByHashAndSourceFolder($hash, $file);    
    
            if(!$file_already_exists){
                
                $file_m_time = @filemtime($file);

                $mime = \KlinkDocumentUtils::get_mime($file);
    
                $file_model = new File();
                $file_model->name = basename($file);
                $file_model->hash = $hash;
                $file_model->mime_type = $mime; 
                $file_model->size = Storage::size($file);
                $file_model->thumbnail_path = null;
                $file_model->path = $file;
                $file_model->user_id = $this->import->user_id;
                $file_model->original_uri = $original_file;
                $file_model->is_folder = false;
                    
                if(!$file_m_time){
                    $file_model->created_at = \Carbon\Carbon::createFromFormat('U', $file_m_time);
                }
                $file_model->save();
    
                Log::info('ImportCommand file entry created ' . $file_model->id , array('file_model' => $file_model));
    
                try{

                    $descriptor = $this->service->indexDocument( $file_model, $visibility, null, $this->group );
                    
                    Log::info('ImportCommand document descriptor entry created ' . $descriptor->id , array('descriptor' => $descriptor));

                } catch(\InvalidArgumentException $kex){
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('ImportCommand Indexing error: InvalidArgumentException', array('exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => false));
                } catch(\KlinkException $kex){
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('ImportCommand Indexing error: KlinkException', array('exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => false));
                } catch(\Exception $kex){
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('ImportCommand Indexing error', array('exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => false));
                }

            }
            else if($file_already_exists){
                
                Log::warning('Skipping file import - already exists -', array('import' => $this->import->toArray(), 'import_file' => $folder->toArray(), 'file' => $file));

            }
            
            $count++;
            $this->import->bytes_received = $count;
            $this->import->save();

        }
        
    }
    


    function files($directory, $exclude = null)
	{
		$directories = array();

		foreach (Finder::create()->in($directory)->files()->depth('== 0') as $dir)
		{
			$directories[] = $dir->getPathname();
		}

		return $directories;
	}


    








// 
// 
//         /*
//          * starting point
//          */
//         function downloadShared($root){
//             //if it's a directory, just edit the infos
//             $import = $this->import;
//             $file = File::find($import->file_id);
//             $this->file = $file;
//             if(is_dir($root)){
//                 $file_info = $this->get_file_info('file://'.str_replace("\\", '/', $file->original_uri));
//                 $file->mime_type = 'folder';
//                 $file->size = 1;
//                 $file->name = $file_info['name'];
//                 $file->path = \Config::get('dms.upload_folder')."".$file->id;//for folders, just to make it unique
//                 $file->update();
//                 
//                 $import->status = Import::STATUS_COMPLETED;
//                 $import->status_message = Import::MESSAGE_COMPLETED;
//                 $import->bytes_expected = 1;
//                 $import->bytes_received = 1;
//                 $import->update();
//             }else{//it's not a folder, so treat like a normal remote url
//                 //not working.
//                 $url = 'file://'.str_replace("\\", '/', $file->original_uri);
//                 $this->downloadFile($url,true);
//             }
//         }
// 
// 
//     // (below) Made by others, seems to work so I don't care ----------------------------
// 
//         
//         /*
//         Set Headers
//         Get total size of file
//         Then loop through the total size incrementing a chunck size
//         */
//         function downloadFile($url,$local_request){
//             /*
//              * get the remote file headers
//              */
//             set_time_limit(0);
//             header('Content-Description: File Transfer');
//             header('Content-Type: application/octet-stream');
//             header('Content-disposition: attachment; filename='.basename($url));
//             header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//             header('Expires: 0');
//             header('Pragma: public');
//             $file_info = $this->get_file_info($url);
//             header('Content-Length: '.$file_info['size']);
//             $import = $this->import;
//             $file = File::find($import->file_id);
//             if(!$local_request){
//                 $file->mime_type =$file_info['mimetype'];
//             }else if(mime_content_type($url)!=null){
//                 $file->mime_type= mime_content_type($url);
//             }else{
//                 $file->mime_type= 'undefined';
//             }
//                         
//             $file->size = $file_info['size'] < 0 ? 0 : $file_info['size'];
// 
//             if(isset($file_info['name']) && !empty($file_info['name'])){
//                 $file->name = $file_info['name'];
//             }
//             
//             $ext= $file->mime_type == "undefined" ? 
//                     explode('.',$file->original_uri)[count(explode('.',$file->original_uri)-1)] : 
//                     \KlinkDocumentUtils::isMimeTypeSupported($file->mime_type) ? 
//                         \KlinkDocumentUtils::getExtensionFromMimeType($file->mime_type)
//                         : explode('.',$file->original_uri)[count(explode('.',$file->original_uri))-1]; //get the extension from the original url if ext not found
//             
//             // $file->path = \Config::get('dms.upload_folder').$file->id.".".  $ext; // already calculated before adding to the queue
//             
//             $file->update();
//             $this->file = $file;
//             $import->bytes_expected = $file->size;
//             $import->bytes_received = 0;
//             $import->status = Import::STATUS_QUEUED;
//             $import->status_message = Import::MESSAGE_QUEUED;
//             $import->update();
// 
//             $i = 0;
//             $size = $file_info['size']; //Size is -1 on text/html
// 
//             $good = true;
// 
//             if($size < 0){
// 
//                 try{
// 
//                     file_put_contents($file->path, file_get_contents($url));
// 
//                     $import->status = Import::STATUS_COMPLETED;
//                     $import->status_message = Import::MESSAGE_COMPLETED;
//                     
//                     $import->update();
//                     $file = File::find($file->id);
//                     $file->hash = \KlinkDocumentUtils::generateDocumentHash($file->path); //file downloaded and saved
//                     $file->update();
// 
//                 }catch(\Exception $ex){
// 
//                     $import->status = Import::STATUS_ERROR;
//                     $import->status_message = Import::MESSAGE_ERROR_LOOSE_CHUNKS;
//                     $import->update();
// 
//                     $good = false;
// 
//                 }
// 
//             }
//             else {
// 
//                 while($i<=$size){
//                     //get chunks in order
//                     $this->get_chunk($url,(($i==0)?$i:$i+1),((($i+self::CHUNKSIZE)>$size)?$size:$i+self::CHUNKSIZE));
//                     $i = ($i+self::CHUNKSIZE);
//                 }
//                 $import = Import::find($this->import->id);
//                 if($import->bytes_expected!=$import->bytes_received){
//                     $import->status = Import::STATUS_ERROR;
//                     $import->status_message = Import::MESSAGE_ERROR_LOOSE_CHUNKS;
//                     $good = false;
//                 }else{
//                     $import->status = Import::STATUS_COMPLETED;
//                     $import->status_message = Import::MESSAGE_COMPLETED;
//                 }
//                 $import->update();
//                 $file = File::find($file->id);
//                 $file->hash = \KlinkDocumentUtils::generateDocumentHash($file->path); //file downloaded and saved
//                 $file->update();
//             }
// 
//             return $good;
//         }
// 
//         /**
//          * Callback function for CURLOPT_WRITEFUNCTION
//          */
//         function chunk($ch, $str) {
//             $import = Import::find($this->import->id);
//             $import->status = Import::STATUS_DOWNLOADING;
//             $import->status_message = Import::MESSAGE_DOWNLOADING;
// 
//             $out = fopen($this->file->path,'a+');
//             
//             if($out){
//                 fwrite($out, $str);
//                 $import->bytes_received+=strlen($str);
//                 fclose($out);
//             }
//             $import->update();
//             
//             return strlen($str);
//         }
// 
//         /**
//          * Function to get a range of bytes from the remote file
//          */
//         function get_chunk($file,$start,$end){
//             
//             $callback = function ($ch, $str){
//                 return $this->chunk($ch, $str);
//             };
//             $ch = curl_init();
//             curl_setopt($ch, CURLOPT_URL, $file);
//             curl_setopt($ch, CURLOPT_RANGE, $start.'-'.$end);
//             curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
//             curl_setopt($ch, CURLOPT_WRITEFUNCTION, $callback);
//             $result = curl_exec($ch);
//             curl_close($ch);
//         }
// 
//         /**
//          * Get total size of file
//          */
//         function get_file_info($url){
//             $ch = curl_init();
//             curl_setopt($ch, CURLOPT_URL, $url);
//             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($ch, CURLOPT_HEADER, true);
//             curl_setopt($ch, CURLOPT_NOBODY, true);
//             curl_exec($ch);
//             $name_with_ext = explode('/', $url)[count(explode('/', $url))-1];
//             $name = str_contains($name_with_ext, '.') ? explode('.',$name_with_ext)[0] : $name_with_ext;
//             return array(
//                 'size' => intval(curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD)),
//                 'mimetype' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
//                 'name' => $name // come nome del file, l'ultima parte dell'url
//             );
//         }
//         


}
