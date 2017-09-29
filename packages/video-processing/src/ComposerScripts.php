<?php


namespace OneOffTech\VideoProcessing;

use Composer\Factory;
use Composer\Script\Event;
use Composer\Downloader\TransportException;
use Composer\Util\ProcessExecutor;

class ComposerScripts
{
    /**
     * Maps the OS family (according to PHP) to the file
     * inside the Gitlab artifacts
     */
    private static $file_for_os = [
        'windows' => 'dist/video-processing-cli-win.exe',
        'linux' => 'dist/video-processing-cli-linux',
        'darwin' => 'dist/video-processing-cli-macos'
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
            
            $rfs = Factory::createRemoteFilesystem($io, $event->getComposer()->getConfig());
            
            $os = strtolower(PHP_OS);

            if (isset(static::$map[$os])) {
                $os = static::$map[$os];
            }
            
            $io->write('Downloading the video processing cli binary...');
            
            // grab the required version from the config
            
            $artifact_url = $event->getComposer()->getConfig()->get('video-cli-download-url');
            
            // get the location of the executable we need to download
            
            $url = static::findArtifactPermalink($artifact_url, $rfs);
            
            // download the file
            
            $folder = __DIR__.'/../../../bin/';

            
            $fileName = $folder.'video-processing-cli'.($os==='windows' ? '.exe' : '');

            if (is_file($fileName) && is_file($folder.'binary.lock') && file_get_contents($folder.'binary.lock') === $url) {
                $io->write('');
                $io->write('<warning>video-processing-cli already downloaded, skipping.</warning>');
                $io->write('');
                return;
            }
            
            try {
                $hostname = parse_url($url, PHP_URL_HOST);
                
                $rfs->copy($hostname, $url, $fileName, true);

                if ($os!=='windows') {
                    // making sure the binary is executable
                    chmod($fileName, 0755);
                }

                file_put_contents($folder.'binary.lock', $url);

                // executing the video-processing-cli to fetch its dependencies
                $io->write('');
                $io->write('Installing the video-processing-cli and its dependencies...');
                $io->write('');

                $executor = new ProcessExecutor($io);

                $command = './'.basename($fileName).' fetch:dependencies';

                $exitCode = $executor->execute($command, $executorOutput, $folder);
                
                $io->write($executorOutput);

                if ($exitCode > 0) {
                    $io->writeError($executor->getErrorOutput());
                }
            } catch (TransportException $e) {
                $io->writeError('');
                $io->writeError('    Download failed', true);
                $io->writeError("    {$e->getMessage()}", true);
                $io->writeError('');
            }
        } catch (\Exception $ex) {
            $io->writeError('');
            $io->writeError('    Failed to retrieve the video-processing-cli', true);
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

            $location = rtrim(trim(substr($interesting_headers[0], 9)), 'browse').'file';

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
}
