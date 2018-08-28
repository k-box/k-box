<?php

namespace KBox\Documents\Services;

use KBox\File;
use KBox\DocumentDescriptor;
use Illuminate\Support\Facades\Config;

// todo: rename to StorageStatisticsService
class StorageService
{
    private $storage_data = null;

    /**
     *
     * @return string the used space on disk
     */
    public function used()
    {
        return $this->getStorageData()['used_space_on_docs_folder'];
    }
    
    /**
     *
     * @return int the used space in percentage
     */
    public function usedPercentage()
    {
        return $this->getStorageData()['full_percentage'];
    }
    
    /**
     *
     * @return string the total space of the disk
     */
    public function total()
    {
        return $this->getStorageData()['total_space_on_docs_folder'];
    }

    /**
     * Build the usage graph. The graph is built using document formats
     *
     * @return array an array with keys representing the items in the graph and values an associative array with 'label', 'value' and 'color'
     */
    public function usageGraph()
    {
        $used = $this->getStorageData()['raw_data']['used_docs'];
        $used_percentage = $this->getStorageData()['full_percentage'];

        $real_used = File::get(['size'])->sum('size');

        $images = DocumentDescriptor::local()->where('document_type', 'image')->with('file')->get()->pluck('file.size')->sum();
        $documents = DocumentDescriptor::local()->whereIn('document_type', ['document', 'web-page', 'spreadsheet', 'text-document', 'presentation'])->with('file')->get()->pluck('file.size')->sum();
        $videos = DocumentDescriptor::local()->where('document_type', 'video')->with('file')->get()->pluck('file.size')->sum();

        $other = $used - ($images + $documents + $videos);

        return [
          'documents' => [
            'label' => trans('widgets.storage.graph_labels.documents'),
            'value' => round($documents*$used_percentage/$used, 0),
            'color' => '3498db'
          ],
          'images' => [
            'label' => trans('widgets.storage.graph_labels.images'),
            'value' => round($images*$used_percentage/$used, 0),
            'color' => 'e67e22'
          ],
          'videos' => [
            'label' => trans('widgets.storage.graph_labels.videos'),
            'value' => round($videos*$used_percentage/$used, 0),
            'color' => '9b59b6'
          ],
          'other' => [
            'label' => trans('widgets.storage.graph_labels.other'),
            'value' => round($other*$used_percentage/$used, 0),
            'color' => '95a5a6'
          ],
          
        ];
    }

    // -------------

    private function getStorageData()
    {
        if (is_null($this->storage_data)) {
            $docs_folder = Config::get('dms.upload_folder');

            $app_folder = app_path();

            $free_space = disk_free_space($docs_folder);

            $free_space_app = disk_free_space($app_folder);

            $total_space = disk_total_space($docs_folder);

            $total_space_app = disk_total_space($app_folder);

            $used_space = $total_space - $free_space;

            $free_space_on_docs_folder = DocumentsService::human_filesize($free_space, 0);
            
            $used_space_on_docs_folder = DocumentsService::human_filesize($used_space, 0);

            $free_space_on_app_folder = DocumentsService::human_filesize($free_space_app);

            $total_space_on_docs_folder = DocumentsService::human_filesize($total_space, 0);

            $total_space_on_app_folder = DocumentsService::human_filesize($total_space_app);

            $full_percentage = round(($total_space - $free_space)/$total_space*100);

            $raw_data = [
                'free_app' => $free_space_app,
                'free_docs' => $free_space,
                'total_app' => $total_space_app,
                'total_docs' => $total_space,
                'used_app' => $total_space_app - $free_space_app,
                'used_docs' => $used_space,
            ];

            $base = compact(
                'docs_folder',
                'app_folder',
                'free_space_on_docs_folder',
                'free_space_on_app_folder',
                'used_space_on_docs_folder',
                'total_space_on_docs_folder',
                'total_space_on_app_folder',
                'full_percentage',
                'raw_data'
            );

            $this->storage_data = $base;
        }

        return $this->storage_data;
    }
}
