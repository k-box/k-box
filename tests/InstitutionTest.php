<?php

use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\Institution;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests the Institution class
*/
class InstitutionTest extends TestCase {
    
    use DatabaseTransactions;
    



    public function testInstitutionEquality(){

        $inst = new Institution([
            'klink_id' => str_random(4),
            'email' => 'email@email.com',
            'url' => 'https://something.com',
            'type' => 'Organization',
            'thumbnail_uri' => 'https://something.com/logo.png',
            'phone' => '123456789',
            'address_street' => 'a string',
            'address_country' => 'a string',
            'address_locality' => 'a string',
            'address_zip' => 'a string',
            'name' => 'Institution Name'
        ]);

        $this->assertTrue($inst->equal($inst), 'Institution not equal to itself');

        $klinkInst = $inst->toKlinkInstitutionDetails();

        $this->assertTrue($inst->equal($klinkInst), 'Institution not equal to itself after conversion to KlinkInstitutionDetails');
    }

}