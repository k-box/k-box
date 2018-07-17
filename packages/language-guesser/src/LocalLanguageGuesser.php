<?php

namespace OneOffTech\LanguageGuesser;

use OneOffTech\LanguageGuesser\Drivers\LanguageCli;
use OneOffTech\LanguageGuesser\Contracts\LanguageGuesser as LanguageGuesserContract;

class LocalLanguageGuesser implements LanguageGuesserContract
{
    /**
     * Language blacklist.
     * The languages to exclude from the guessing.
     * In this case we exclude languages with less than 4 milion users
     * except from Tajik and Kirgiz
     */
    private $blacklist = ['cat','sot','kat','bcl','glg','lao','lit','umb','tsn','vec','nso','ban','bug','knc','kng','ibb','lug','ace','bam','tzm','ydd','kmb','lun','shn','war','dyu','wol','nds','mkd','vmw','zgh','ewe','khk','slv','ayr','bem','emk','bci','bum','epo','pam','tiv','tpi','ven','ssw','nyn','kbd','iii','yao','lav','quz','src','rup','sco','tso','rmy','men','fon','nhn','dip','kde','snn','kbp','tem','toi','est','snk','cjk','ada','aii','quy','rmn','bin','gaa','ndo'];

    public function guess($text)
    {
        $cli = new LanguageCli($text, false, $this->blacklist);
        $out = $cli->run();

        return substr(trim($out), 0, 3);
    }

    public function isInstalled()
    {
        return LanguageCli::isInstalled();
    }
}
