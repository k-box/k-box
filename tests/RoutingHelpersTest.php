<?php

use KlinkDMS\RoutingHelpers;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test the RoutingHelpers class
*/
class RoutingHelpersTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    
    public function filter_search_data_provider()
    {
        return [
            // $empty_url, $current_active_filters, $facet, $term, $selected, $expected_url
            ['', [], 'language', 'en', false, '?language=en'],
            ['?', [], 'language', 'en', false, '?language=en'],
            ['?s=pasture', [], 'language', 'en', false, '?s=pasture&language=en'],
            ['?s=pasture', ['language' => ['en']], 'language', 'en', true, '?s=pasture'],
            
        ];
    }

    /**
     * @dataProvider filter_search_data_provider
     */
    public function testFilterSearch($empty_url, $current_active_filters, $facet, $term, $selected, $expected_url)
    {
        $url = RoutingHelpers::filterSearch($empty_url, $current_active_filters, $facet, $term, $selected);

        $this->assertEquals($expected_url, $url);
    }
}
