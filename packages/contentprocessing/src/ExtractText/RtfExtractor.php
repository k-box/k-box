<?php

namespace Content\ExtractText;

use Content\Contracts\ExtractText as ExtractTextContract;

/**
 *
 */
class RtfExtractor implements ExtractTextContract
{
    private $path = null;

    private $reader = null;
    
    public function __construct()
    {
    }

    public function load($path)
    {
        $this->path = $path;

        return $this;
    }

    public function text()
    {
        
        // super inspired by https://github.com/joshribakoff/rtf/blob/master/Note/RTFToPlainText.php
        
        $content = file_get_contents($path);
        
        $rtf = $utf8_content = mb_convert_encoding(
            $content,
            'UTF-8',
              mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true)
        );
        
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

    public function properties()
    {
        return null;
    }

    public function supportedMimeTypes()
    {
        return ['application/rtf'];
    }
}
