<?php

namespace KBox\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\InteractsWithTime;

class ReadonlyMode
{
    use InteractsWithTime;

    /**
     * Get the path of the local storage
     *
     * @return string
     */
    protected function storagePath()
    {
        return storage_path('');
    }
    
    /**
     * Get the path of the readonly file that
     * contains the configuration for
     * activating the readonly mode
     *
     * @return string
     */
    public function readonlyPath()
    {
        return $this->storagePath().'/framework/readonly';
    }

    /**
     * Check if the readonly mode is active
     *
     * @return boolean
     */
    public function isReadonlyActive()
    {
        return file_exists($this->readonlyPath());
    }
       
    /**
     * Activate the readonly mode
     *
     * @return ReadonlyMode
     */
    public function activate($config = [])
    {
        $data = array_merge([
            'time' => $this->currentTime(),
            'retry' => 86400,
            'message' => 'This application is in readonly mode.',
        ], $config);
    
        file_put_contents($this->readonlyPath(), json_encode($data, JSON_PRETTY_PRINT));
    
        return $this;
    }

    /**
     * Deactivate the readonly mode
     *
     * @return ReadonlyMode
     */
    public function deactivate()
    {
        if ($this->isReadonlyActive()) {
            unlink($this->readonlyPath());
        }

        return $this;
    }

    /**
     * Get the readonly mode configuration
     *
     * @return array
     */
    public function getReadonlyConfiguration()
    {
        return json_decode(file_get_contents($this->readonlyPath()), true);
    }
}
