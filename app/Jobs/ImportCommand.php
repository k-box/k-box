<?php

namespace KBox\Jobs;

use Illuminate\Console\Command;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use KBox\Import;
use KBox\User;
use KBox\File;
use KBox\Group;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File as Storage;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use Klink\DmsAdapter\KlinkDocumentUtils;
use Klink\DmsAdapter\KlinkVisibilityType;
use Klink\DmsAdapter\Exceptions\KlinkException;

class ImportCommand extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
        
    const CHUNKSIZE = 2048; //chunk size
        
    private $user;
    private $url;
    private $import;
    private $group;
    private $copy;
    private $exclude;
    
    private $service = null;
    
    private $output = null;
    
    private $file_already_exists_resolution_enabled = false;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user, Import $import, Group $group = null, $copy = false, $exclude_folders = null, OutputInterface $output = null)
    {
        $this->user = $user;
        $this->url = null;
        $this->import = $import;
        $this->copy = $copy;
        $this->group = $group;
        $this->exclude = $exclude_folders;
        $this->output = $output;
    }
    
    /**
     * When a file being imported Already exists, by it's hash, the system will attempt to add the new collection to the existing file
     */
    public function useFileConflictResolution()
    {
        $this->file_already_exists_resolution_enabled = true;
        return $this;
    }
    
    public function isFileConflictResolutionActive()
    {
        return $this->file_already_exists_resolution_enabled;
    }
        
    /**
     * Execute the command.
     *
     * Peforms import
     */
    public function handle(\Klink\DmsDocuments\DocumentsService $documentsService)
    {
        try {
            Log::info('Executing ImportCommand', [
                'import' => $this->import,'user' => $this->user,
                'group' => $this->group,
                'copy' => $this->copy,
                'exclude' => $this->exclude,
                'job_id' => ! is_null($this->job) ? $this->job->getJobId() : 'commandline'
            ]);
            
            $this->service = $documentsService;
                        
            if ($this->import->is_remote) {
                throw new Exception('Import from URL are not supported');
            } else {
                $this->doImportFolder();
            }
    
            $this->import->status = Import::STATUS_COMPLETED;
            $this->import->status_message = Import::MESSAGE_COMPLETED;
    
            $this->import->save();
        } catch (Exception $kex) {
            Log::error('ImportCommand: unhandled Exception while importing', ['exception' => $kex, 'import' => $this->import->toArray()]);
            
            $this->import->status = Import::STATUS_ERROR;
            $this->import->status_message = Import::MESSAGE_ERROR;
            $this->import->message = $kex->getMessage();
            $this->import->payload = [ 'error' => class_basename($kex).' in '.basename($kex->getFile()).' line '.$kex->getLine().': '.$kex->getMessage() ];
            
            if (! is_null($this->job)) {
                $this->import->job_payload = $this->job->getRawBody(); //save the original job payload
            }
    
            $this->import->save();
            
            $this->line('  >>> JOB FAILED: import '.$this->import->id.' Processed '.$this->import->bytes_received.'/'.$this->import->bytes_expected);
            
            $this->fail();
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
    
    public function doImportFolder()
    {
        $folder = $this->import->file;
            
        $visibility = KlinkVisibilityType::KLINK_PRIVATE;

        $files = $this->files(realpath($folder->original_uri), $this->exclude);

        $this->import->bytes_expected = count($files);
        
        $this->line('Preparing import from '.$folder->original_uri);
        $this->line('  '.$this->import->bytes_expected.' files found');

        $this->import->status = Import::STATUS_DOWNLOADING;
        $this->import->status_message = Import::MESSAGE_DOWNLOADING;

        $this->import->save();

        $file_model = null;
        $count = 0;
        $descriptor = null;
        $file = null;

        foreach ($files as $original_file) {
            $file = $original_file;
            
            $this->line('  Importing '.$original_file);
            
            if ($this->copy) {
                $file = $folder->path.'/'.basename($original_file);
                
                $copied = @copy($original_file, $file);
                
                if (! $copied) {
                    $errors= error_get_last();
                    
                    Log::error('File cannot be copied', ['folder' => $folder, 'file' => $file, 'original_file' => $original_file, 'constructed_path' => $folder->path.'/'.basename($original_file)]);

                    throw new \Exception('Document '.basename($original_file).' cannot be copied ('.$original_file.' '.(isset($errors['message'])?$errors['message']:'').')');
                }
            }
                
            $hash = KlinkDocumentUtils::generateDocumentHash($file);
    
            $file_found = File::where('hash', $hash)->first();
            $file_already_exists = ! is_null($file_found);
    
            if (! $file_already_exists) {
                $file_m_time = @filemtime($file);

                $mime = KlinkDocumentUtils::get_mime($file);
    
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
                    
                if (! $file_m_time) {
                    $file_model->created_at = \Carbon\Carbon::createFromFormat('U', $file_m_time);
                }
                $file_model->save();
                
                Log::info('ImportCommand file entry created '.$file_model->id, ['file_model' => $file_model]);
    
                try {
                    $descriptor = $this->service->indexDocument($file_model, $visibility, null, $this->group);
                    
                    Log::info('ImportCommand document descriptor entry created '.$descriptor->id, ['descriptor' => $descriptor]);
                    
                    $this->line('  done. ');
                } catch (\InvalidArgumentException $kex) {
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('ImportCommand Indexing error: InvalidArgumentException', ['exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => false]);
                    $this->line('  Error:  '.$kex->getMessage());
                } catch (KlinkException $kex) {
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('ImportCommand Indexing error: KlinkException', ['exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => false]);
                    $this->line('  Error:  '.$kex->getMessage());
                } catch (\Exception $kex) {
                    // if cannot be indexed is not a real problem here thanks to the status of the DocumentDescriptor everyhting can be solved
                    // at a later time
                    Log::error('ImportCommand Indexing error', ['exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'is_remote' => false]);
                    $this->line('  Error:  '.$kex->getMessage());
                }
            } elseif ($file_already_exists) {
                $this->line('  > File already exists.');
                $this->line('  > Found '.$file_found->id.':'.$file_found->name.' at '.$file_found->path);
                
                if ($this->isFileConflictResolutionActive()) {
                    $this->line('  > Attempting to merge document descriptors... ');
                    
                    $descriptor = DocumentDescriptor::where('file_id', $file_found->id)->first();
                    
                    if (is_null($descriptor)) {
                        $this->line('  > No descriptor found for file '.$file_found->id.'. ');
                        
                        throw new Exception('File already exists. The file '.$file.' has the same fingerprint of '.$file_found->id.':'.$file_found->name.' at '.$file_found->path);
                    }
                    
                    try {
                        $descriptor->abstract = (! is_null($descriptor->abstract) ? $descriptor->abstract : '').'also named '.basename($file);
                        $descriptor->save();
                        
                        $this->service->addDocumentToGroup($this->user, $descriptor, $this->group, true);
                        
                        // Log::info('ImportCommand document descriptor entry created ' . $descriptor->id , array('descriptor' => $descriptor));
                        
                        $this->line('  >   done. ');
                    } catch (\Exception $kex) {
                        Log::error('ImportCommand Indexing error, while merging descriptors for existing file', ['exception' => $kex, 'import' => $this->import->toArray(), 'import_file' => $file, 'descriptor' => $descriptor, 'file_found' => $file_found]);
                        $this->line('  Error:  '.$kex->getMessage());
                    }
                } else {
                    Log::warning('Skipping file import - already exists -', ['import' => $this->import->toArray(), 'import_file' => $folder->toArray(), 'file' => $file]);
                    throw new Exception('File already exists. The file '.$file.' has the same fingerprint of '.$file_found->id.':'.$file_found->name.' at '.$file_found->path);
                }
            }
            
            $count++;
            $this->import->bytes_received = $count;
            $this->import->save();
        }
    }
    
    public function files($directory, $exclude = null)
    {
        $directories = [];

        foreach (Finder::create()->in($directory)->files()->notName('Thumbs.db')->depth('== 0') as $dir) {
            $directories[] = $dir->getPathname();
        }

        return $directories;
    }
    
    public function line($text)
    {
        if (! is_null($this->output)) {
            $this->output->writeln($text, OutputInterface::VERBOSITY_NORMAL);
        }
    }
}
