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
final class WindowsDriver extends Driver
{

    const RASTER_INFO_EXECUTABLE = 'gdalinfo.exe';
    
    const VECTOR_INFO_EXECUTABLE = 'ogrinfo.exe';

    private function execute($command, $inputs = [], $options = [])
    {
        $sdk_root = base_path(self::BIN_FOLDER);

        $env = [
            "PATH" => "$sdk_root\bin;$sdk_root\bin\gdal\python\osgeo;$sdk_root\bin\proj\apps;$sdk_root\bin\gdal\apps;$sdk_root\bin\ms\apps;$sdk_root\bin\gdal\csharp;$sdk_root\bin\ms\csharp;$sdk_root\bin\curl;",
            "GDAL_DATA" => "$sdk_root\bin\gdal-data",
            "GDAL_DRIVER_PATH" => "$sdk_root\bin\gdal\plugins",
            "PYTHONPATH" => "$sdk_root\bin\gdal\python;$sdk_root\bin\ms\python",
            "PROJ_LIB" => "$sdk_root\bin\proj\SHARE",
        ];

        $arguments = array_map(function($in){
            return "\"$in\"";
        }, $inputs);
        $this->process = $process = new Process(
            sprintf('"%1$s" %3$s %2$s', $command, implode(" ", $arguments), implode(" ", $options)),
            $sdk_root,
            $env
        );
        
        $process->setTimeout(40);
        $process->setIdleTimeout(40);
        $process->mustRun();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        return $process->getOutput();
            
    }

    public function version()
    {
        return trim($this->execute(self::RASTER_INFO_EXECUTABLE, [], ['--version']), " \t\n\r\0\x0B\x0C");
    }

    public function info($path)
    {
        $geoFile = GeoFile::from($path);

        $result = $this->execute($geoFile->type === GeoType::RASTER ? self::RASTER_INFO_EXECUTABLE : self::VECTOR_INFO_EXECUTABLE, [$path], $geoFile->type === GeoType::RASTER ? ['-json', '-proj4'] : ['-al', '-so', '-nomd']);
        
        if($geoFile->type === GeoType::RASTER){
            return GeoProperties::fromGdalOutput($result);
        }

        return GeoProperties::fromOgrOutput($result);
    }

}
