<?php

namespace KBox\Geo\Gdal\Drivers;

use RuntimeException;
use KBox\Geo\GeoFile;
use KBox\Geo\GeoType;
use KBox\Geo\GeoProperties;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * GDAL binaries wrapper
 * 
 * Abstract the invokation of the GDAL based on tasks
 */
final class LinuxDriver extends Driver
{

    const RASTER_INFO_EXECUTABLE = 'gdalinfo';
    
    const VECTOR_INFO_EXECUTABLE = 'ogrinfo';

    const VECTOR_CONVERT_EXECUTABLE = 'ogr2ogr';

    const RASTER_CONVERT_EXECUTABLE = 'gdal_translate';

    protected function execute($command, $inputs = [], $options = [])
    {
        $arguments = array_map(function($in){
            return "\"$in\"";
        }, $inputs);
        $this->process = $process = new Process(
            sprintf('"%1$s" %3$s %2$s', $command, implode(" ", $arguments), implode(" ", $options)),
            base_path()
        );
        
        $process->setTimeout(40);
        $process->setIdleTimeout(40);
        $process->mustRun();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        return $process->getOutput();
    }

}
