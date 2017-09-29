<?php

namespace KlinkDMS\Jobs;

use Log;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use KlinkDMS\DocumentDescriptor;
use OneOffTech\VideoProcessing\VideoProcessorFactory;

class ConvertVideo
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The pointer tp the Document Descriptor that represent the video to be converted
     *
     * @var \KlinkDMS\DocumentDescriptor
     */
    public $descriptor = null;

    /**
     * Create a new job instance.
     *
     * @param \KlinkDMS\DocumentDescriptor $descriptor The descriptor, that represents a video file, to convert
     * @return void
     */
    public function __construct(DocumentDescriptor $descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $videoFile = $this->descriptor->file;

            \Log::info('ConvertVideo', ['video' => $videoFile, 'mime' => $videoFile->mime_type, 'equal' => $videoFile->mime_type === 'video/mp4']);

            if ($videoFile->mime_type === 'video/mp4') {
                // prepare video for conversion only if is an mp4 file
                \Log::info('About to call the VideoProcessorFactory');

                $videoProcessor = app()->make(VideoProcessorFactory::class)->make();
    
                $out = $videoProcessor->streamify($videoFile->absolute_path);

                \Log::info('VideoCovert streamify', ['out' => $out]);
            }
        } catch (Exception $ex) {
            Log::error('Video conversion error', ['descriptor' => $this->descriptor, 'error' => $ex]);
        }
    }
}
