<?php

namespace KBox\Documents;

use PharData;
use ZipArchive;
use Composer\Factory;
use Composer\Script\Event;
use Composer\Downloader\TransportException;

class ComposerScripts
{
    const DOWNLOAD_URL = 'https://dl.xpdfreader.com/';

    /**
     * Maps the OS family (according to PHP) to the required file
     */
    private static $file_for_os = [
        'windows' => 'xpdf-tools-win-4.03.zip',
        'linux' => 'xpdf-tools-linux-4.03.tar.gz',
        'darwin' => 'xpdf-tools-mac-4.03.tar.gz'
    ];

    private static $version = [
        'windows' => 'xpdf-tools-win-4.03',
        'linux' => 'xpdf-tools-linux-4.03',
        'darwin' => 'xpdf-tools-mac-4.03'
    ];

    private static $architecture = 'bin64';

    private static $files_to_extract = [
        'pdfinfo',
        'pdftotext',
    ];

    private static $additional_files_to_extract = [
        'README',
        'COPYING',
        'COPYING3',
    ];

    /**
     * normalize the PHP_OS value
     */
    private static $map = ['winnt' => 'windows'];

    /**
     * Download the Video Processing CLI binary.
     * Must be called from a Composer script
     */
    public static function postInstall(Event $event)
    {
        try {
            $io = $event->getIO();

            $composer_config = $event->getComposer()->getConfig();
            
            $rfs = Factory::createRemoteFilesystem($io, $composer_config);
            
            $os = strtolower(PHP_OS);

            if (isset(static::$map[$os])) {
                $os = static::$map[$os];
            }
            
            $io->write('Downloading PDF tools...');
            
            // grab the required version from the config
            
            if (! isset(static::$file_for_os[$os])) {
                $expected = implode(',', array_keys(self::$file_for_os));
                throw new \Exception("OS family not supported. Found $os, expected [$expected].");
            }
            
            $archive_url = self::DOWNLOAD_URL.static::$file_for_os[$os];
            
            // download the file
            
            $folder = __DIR__.'/../../../bin/';

            $fileName = $folder.static::$file_for_os[$os];
            
            try {
                // download archive

                $hostname = parse_url($archive_url, PHP_URL_HOST);
                
                $rfs->copy($hostname, $archive_url, $fileName, true);

                $io->write('');
                $io->write('Installing PDF tools...');

                if ($os !== 'windows' && ! class_exists('PharData')) {
                    throw new Exception('Unable to find PharData class for extracting files from compressed archive.');
                }
                
                if ($os === 'windows' && ! class_exists('ZipArchive')) {
                    throw new Exception('Unable to find ZipArchive class for extracting files from compressed archive.');
                }
                
                $path_inside_archive = static::$version[$os].'/'.static::$architecture;

                $archive = $os === 'windows' ? static::getZipArchive($fileName) : new PharData($fileName);

                // extract executables

                $extract = array_map(function ($f) use ($os, $path_inside_archive) {
                    return $path_inside_archive.'/'.$f.($os==='windows' ? '.exe' : '');
                }, static::$files_to_extract);

                $archive->extractTo($folder, $extract);

                foreach ($extract as $file) {
                    rename($folder.$file, $folder.basename($file));
                }
                
                // extract license files

                $additional_extract = array_map(function ($f) use ($os, $path_inside_archive) {
                    return static::$version[$os].'/'.$f;
                }, static::$additional_files_to_extract);

                $archive->extractTo($folder, $additional_extract);

                foreach ($additional_extract as $file) {
                    rename($folder.$file, $folder.'xpdf-'.basename($file));
                }

                $archive = null; // this disposes the resources used by PharData and let PHP remove the lock on the file

                // remove the folder and the zip

                @rmdir($folder.$path_inside_archive);
                @rmdir(dirname($folder.$path_inside_archive));
                @unlink(realpath($fileName));
            } catch (TransportException $e) {
                $io->writeError('');
                $io->writeError('    Download failed', true);
                $io->writeError("    {$e->getMessage()}", true);
                $io->writeError('');
            }
        } catch (\Exception $ex) {
            $io->writeError('');
            $io->writeError('    Failed to retrieve the content processing dependencies', true);
            $io->writeError("    {$ex->getMessage()}", true);
            $io->writeError('');
        }
    }
        
    private static function findArtifactPermalink($url, $rfs)
    {
        try {
            $hostname = parse_url($url, PHP_URL_HOST);
            
            $content = $rfs->getContents($hostname, $url, false);
            
            $headers = $rfs->getLastHeaders();
                
            $interesting_headers = array_values(array_filter($headers, function ($h) {
                return strpos($h, 'Location') !== false;
            }));

            if (empty($interesting_headers)) {
                throw new \Exception('Expecting to find the location of the artifact package, but got nothing');
            }

            $location_header = array_pop($interesting_headers);

            $location = rtrim(trim(substr($location_header, 9)), 'browse').'raw';

            $os = strtolower(PHP_OS);
            
            if (isset(static::$map[$os])) {
                $os = static::$map[$os];
            }
            
            if (isset(static::$file_for_os[$os])) {
                return $location.'/'.static::$file_for_os[$os];
            } else {
                $expected = implode(',', array_keys(self::$file_for_os));
                throw new \Exception("OS family not supported. Found $os, expected [$expected].");
            }
        } catch (TransportException $e) {
            throw new \Exception("Failed to retrieve available binaries location: {$e->getMessage()}");
        }
    }

    private static function getZipArchive($filename)
    {
        $zip = new ZipArchive;
        
        if (! $zip->open($filename)) {
            throw new Exception("Failed to open $filename");
        }

        return $zip;
    }
}
