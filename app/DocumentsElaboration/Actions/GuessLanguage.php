<?php

namespace KlinkDMS\DocumentsElaboration\Actions;

use Log;
use Exception;
use KlinkDMS\Contracts\Action;
use Rinvex\Language\LanguageLoader;
use Content\Services\TextExtractionService;
use OneOffTech\LanguageGuesser\LanguageGuesserFactory;
use Content\Preview\Exception\UnsupportedFileException;

class GuessLanguage extends Action
{
    protected $canFail = true;

    public function run($descriptor)
    {
        if (! LanguageGuesserFactory::isInstalled() || $descriptor->language !== null) {
            return $descriptor;
        }
        
        $file = $descriptor->file;

        Log::info("Guess language action triggered: {$descriptor->id}, file: $file->id");
        
        if ($file->isVideo()) {
            return $descriptor;
        }

        $guesser = app()->make(LanguageGuesserFactory::class)->make();

        $text = null;
        
        try {
            $textExtractor = app(TextExtractionService::class)->load($file->absolute_path);
            $text = $textExtractor->text();
        } catch (UnsupportedFileException $ex) {
            Log::warning("Cannot extract text from file for language guessing. Unsupported file.", ['file' => $file, 'error' => $ex]);
        }

        if (! $text) {
            // no text to use, return
            return $descriptor;
        }

        try {

            // blacklisting the languages with less than 4 milion users except from Tajik and Kyrgyz
            $language_code = $guesser->guess($text, ['cat','sot','kat','bcl','glg','lao','lit','umb','tsn','vec','nso','ban','bug','knc','kng','ibb','lug','ace','bam','tzm','ydd','kmb','lun','shn','war','dyu','wol','nds','mkd','vmw','zgh','ewe','khk','slv','ayr','bem','emk','bci','bum','epo','pam','tiv','tpi','ven','ssw','nyn','kbd','iii','yao','lav','quz','src','rup','sco','tso','rmy','men','fon','nhn','dip','kde','snn','kbp','tem','toi','est','snk','cjk','ada','aii','quy','rmn','bin','gaa','ndo']);
        
            // transform the language code to ISO 639-1
            $language = LanguageLoader::where('iso_639_3', '=', $language_code);

            if ($language) {
                $language = array_first($language);
    
                $descriptor->language = $language['iso_639_1'];
    
                $descriptor->save();
            } else {
                Log::warning("Cannot find language from ISO code $language_code");
            }
        } catch (Exception $ex) {
            Log::error('Error while guessing language from text', ['descriptor' => $descriptor, 'error' => $ex]);
            throw $ex;
        }

        return $descriptor;
    }
}
