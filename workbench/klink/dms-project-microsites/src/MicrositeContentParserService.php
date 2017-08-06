<?php

namespace Klink\DmsMicrosites;

use GuzzleHttp\Client;

/**
 * Parses the makdown content of the microsite and
 * convert it to HTML
 */
final class MicrositeContentParserService
{

    /**
     * XML Feed to JSON API Endpoint
     */
    const FEED_XML_TO_JSON_API_URL = 'http://rss2json.com/api.json?rss_url=';

    /**
     * Regular expression to extract the @rss tag
     */
    const RSS_CUSTOM_TAG_REGEXP = '/^@rss:([https:\/\/].*)$/m';

    /**
     * @var \Illuminate\Cache\CacheManager
     */
    private $cache = null;

    /**
     * Create a new instance.
     *
     * @param \Illuminate\Cache\CacheManager $cache
     * @return void
     */
    public function __construct(\Illuminate\Cache\CacheManager $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Transform the MicrositeContent in its corresponding HTML representation.
     *
     * support Markdown syntax
     * support custom @rss:https://domain rule to include directly a styled version of the RSS feed
     *
     * To save computational resource the output of the page is cached for 1 hour. The RSS tags expansion
     * could have a different caching time due to the usage of an external service called http://rss2json.com
     *
     * @param MicrositeContent $entry
     * @return string
     */
    public function toHtml(MicrositeContent $entry)
    {
        $content = $this->cache->remember('micrositecontent-'.$entry->id, 60, function () use ($entry) {
            $entry_content = $this->expandRssTag($entry->content);
        
            $content = \Markdown::convertToHtml($entry_content);
            
            return $content;
        });

        return $content;
    }

    /**
     * Expands the custom @rss tag in order to embed the RSS content inside the page.
     *
     * Extract every @rss: and pass to feed URL to a RSS to JSON converter api to retrieve the json
     *
     * <code>
     * @rss:https://url-of-a-feed
     * </code>
     *
     * @param string $entry the original text of the page (must not be elaborated by the markdown parser)
     * @return string the elaborated content with the expansion of the RSS tags in markdown format
     */
    private function expandRssTag($entry)
    {
        preg_match_all(self::RSS_CUSTOM_TAG_REGEXP, $entry, $matches, PREG_SET_ORDER);
        
        if (! empty($matches)) {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => self::FEED_XML_TO_JSON_API_URL,
                // You can set any number of default request options.
                'timeout'  => 2.0,
            ]);
            
            $response = null;
            $body = null;
            
            foreach ($matches as $match) {
                if (! empty($match[0])) {
                    $response = $client->request('GET', self::FEED_XML_TO_JSON_API_URL.trim($match[1]).'?t='.time(), ['verify' => false]);

                    $body = (string) $response->getBody();
                    
                    $body = json_decode($body);
                    
                    $substitution = sprintf('[%1$s](%1$s)', trim($match[1]));
                    
                    if ($body && property_exists($body, 'status') && property_exists($body, 'feed') && $body->status === 'ok') {
                        $entries = $body->items;
                        
                        $substitution = [];
                        
                        foreach ($entries as $en) {
                            $en = sprintf('# [%1$s](%2$s)'.PHP_EOL.' %3$s',
                                $en->title,
                                $en->link,
                                $en->content
                            );
                                                        
                            $substitution[] = $en;
                        }
                        
                        $entry = str_replace($match[0], implode(PHP_EOL.PHP_EOL, $substitution), $entry);
                        
                        $this->cache->put('microsite-'.trim($match[1]), implode(PHP_EOL.PHP_EOL, $substitution), 24 * 60);
                    } else {
                        \Log::warning('MicrositeContent parsing RSS tag error. Cannot get RSS as json due to parsing error', ['rss' => $match[1], 'body' => $body]);
                        
                        $substitution = $this->cache->get('microsite-'.trim($match[1]), $substitution);
                        
                        $entry = str_replace($match[0], $substitution, $entry);
                    }
                }
            }
        }
        
        
        return $entry;
    }
}
