<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\RoutingHelpers;
use Illuminate\Support\Facades\Artisan;
use KlinkDMS\Exceptions\ForbiddenException;


use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/*
 * Test the RoutingHelpers class
*/
class RoutingHelpersTest extends TestCase {
    
    use DatabaseTransactions;
    
    
    public function filter_search_data_provider() {
        return array( 
            // $empty_url, $current_active_filters, $facet, $term, $selected, $expected_url
            array('', [], 'language', 'en', false, '?language=en'),
            array('?', [], 'language', 'en', false, '?language=en'),
            array('?s=pasture', [], 'language', 'en', false, '?s=pasture&language=en'),
            array('?s=pasture', ['language' => ['en']], 'language', 'en', true, '?s=pasture'),
			
		);
    }


    /**
     * @dataProvider filter_search_data_provider
     */
    public function testFilterSearch($empty_url, $current_active_filters, $facet, $term, $selected, $expected_url){

        $url = RoutingHelpers::filterSearch($empty_url, $current_active_filters, $facet, $term, $selected);

        $this->assertEquals($expected_url, $url);
        

    }
    
}