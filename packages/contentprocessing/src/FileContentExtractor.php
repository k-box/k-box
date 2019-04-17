<?php

namespace KBox\Documents;

use KBox\File;
use KBox\Documents\Facades\Files;

/**
 * Basic Textual content extractor from files that are not indexable by the K-Link Core
 * @deprecated
 */
class FileContentExtractor
{
    public function __construct()
    {
    }
    
    /**
     * Utility Function: retrieve the file content or path depending on the fact that the file is indexable.
     *
     * If the the File is indexable the path will be returned so the file content can be sent automatically to the core. Otherwise do the best to get the text from the document.
     *
     * In case of errors or problems the file name will be returned
     *
     * @param KBox\File $file The file to be indexed
     * @return string the file path or the textual content
     * @throws InvalidArgumentException if the mime type is null, empty or not a string
     */
    public function extract($mimeType, $filePath, $default = null)
    {
        $extension = Files::extensionFromType($mimeType);
        
        if (method_exists($this, $extension)) {
            try {
                return $this->{$extension}($filePath);
            } catch (\Exception $ex) {
                \Log::error('Error extracting text from file', ['context' => 'FileContentExtractor', 'param' => compact('mimeType', 'extension', 'filePath'), 'exception' => $ex]);
                
                return is_null($default) ? $this->tokenizeDefault(basename($filePath)) : $this->tokenizeDefault($default);
            }
        }
        
        // extension from mime => invoke extension named method
        
        return is_null($default) ? $this->tokenizeDefault(basename($filePath)) : $this->tokenizeDefault($default);
    }
    
    public function openAsText($path)
    {
        return $this->utf8_file_get_contents($path);
    }
    
    private function tokenizeDefault($default)
    {
        $names = [$default];
            
        $parts = preg_split('/(\-|_|,|\.)/', $default);
        
        $names = array_merge($names, $parts);
        
        return implode(' ', array_filter($names));
    }
    
    private function utf8_file_get_contents($path)
    {
        $content = file_get_contents($path);
                
        return $utf8_content = mb_convert_encoding(
            $content,
            'UTF-8',
            mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)
        );
    }

    // File content extraction methods
    
    /**
     * Extract the textual content from a TXT file.
     *
     * Extract the indexable content from a plain TXT file.
     *
     * @param string $file The file path
     * @return string the file textual content
     */
    private function txt($path)
    {
        return $this->utf8_file_get_contents($path);
    }
    
    /**
     * Extract the textual content from a Markdown file.
     *
     * Extract the indexable content from a Markdown file.
     *
     * @param string $file The file path
     * @return string the file textual content
     */
    private function md($path)
    {
        return $this->utf8_file_get_contents($path);
    }
    
    /**
     * Extract the textual content from a Rich Text Format (RTF) file.
     *
     * Extract the indexable content from a Rich Text Format file.
     *
     * @param string $file The file path
     * @return string the file textual content
     */
    private function rtf($path)
    {
        
        // super inspired by https://github.com/joshribakoff/rtf/blob/master/Note/RTFToPlainText.php
        
        $rtf = $this->utf8_file_get_contents($path);
        
        $tokens = [
            'T_NOOP' => '\\\\uc1|\\\\uc2|\\\\pard|\\\\f[0-9]+|\\\\fs[0-9]+|\\\\viewkind[0-4]+',
            'T_UNDERLINE_END' => '\\\\ulnone',
            'T_UNDERLINE_START' => '\\\\ul',
            'T_BOLD_END' => '\\\\b0',
            'T_BOLD_START' => '\\\\b',
            'T_ITALICS_END' => '\\\\i0',
            'T_ITALICS_START' => '\\\\i',
            'T_COLOR_DEFAULT' => '\\\\cf0',
            'T_COLOR' => '\\\\cf([0-9]+)',
        ];
        
        $plaintext = str_replace("\n", '', $rtf);
        foreach ($tokens as $pattern) {
            $plaintext = preg_replace('#'.$pattern.'#s', '', $plaintext);
        }
        $plaintext = str_replace("\\par", PHP_EOL, $plaintext);
        
        return join(PHP_EOL, array_map("trim", explode(PHP_EOL, $plaintext)));
    }
    
    /**
     * Extract the textual content from a Keyhole Markup Language (KML) file.
     *
     * Extract the `name` and `description` tags contained in the Keyhole Markup Language file (also known as Google Earth file).
     *
     * @param string $file The file path
     * @return string the file textual content
     */
    private function kml($path)
    {
        
        // https://developers.google.com/kml/documentation/kmlreference
        
        $content = $this->utf8_file_get_contents($path);
        
        if ($content === false) {
            return $this->tokenizeDefault(basename($filePath));
        }
        
        $name_preg_int = preg_match_all('/<name>(.*)<\/name>/', $content, $name_matches);
        
        $descr_preg_int = preg_match_all('/<description>(.*)<\/description>/', $content, $description_matches);
        
        $names = '';
        $descriptions = '';
        
        if ($name_preg_int > 0) {
            $names = implode(PHP_EOL, $name_matches[1]);
        }
        
        if ($descr_preg_int > 0) {
            $descriptions = implode(PHP_EOL, $description_matches[1]);
        }
        
        return $names.PHP_EOL.$descriptions;
    }
    
    /**
     * Extract the textual content from a compressed Keyhole Markup Language (KMZ) file.
     *
     * Extract the `name` and `description` tags from the KML file contained in the compressed archive (KMZ file).
     *
     * @param string $file The file path
     * @return string the file textual content
     */
    private function kmz($path)
    {
        
        // https://developers.google.com/kml/documentation/kmzarchives
        
        if (! class_exists('ZipArchive')) {
            return $this->kml('zip://'.$path.'#doc.xml');
        }
        
        $zip = new \ZipArchive;
        $files = [];
        if ($zip->open($path) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileinfo = pathinfo($filename);
                if ($fileinfo['extension'] === 'kml') {
                    return $this->kml("zip://".$path."#".$filename);
                }
            }
            $zip->close();
        }

        return $this->kml('zip://'.$path.'#doc.xml');
    }
}
