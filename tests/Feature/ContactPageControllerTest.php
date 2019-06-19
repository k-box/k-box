<?php

namespace Tests\Feature;

use KBox\Option;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ContactPageControllerTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testContactShowsFakeDataIfContactsAreNotConfigured()
    {
        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $response = $this->get(route('contact'));

        $response->assertViewIs('static.contact');

        $response->assertDontSee('info@klink.asia');
        $response->assertDontSee('https://klink.asia');
    }
    
    public function testContactPageShowsSavedContactInfo()
    {
        Option::put("contact.name", "An Organization name");
        Option::put("contact.email", "some@email.com");
        Option::put("contact.phone", "+49-123456789");
        Option::put("contact.website", "https://something.asia");
        Option::put("contact.image", "https://something.asia/some.jpg");
        Option::put("contact.address_street", "Street");
        Option::put("contact.address_locality", "Locality");
        Option::put("contact.address_country", "Country");
        Option::put("contact.address_zip", "123456");
        
        $response = $this->get(route('contact'));

        $response->assertViewIs('static.contact');

        $response->assertSee("An Organization name");
        $response->assertSee("some@email.com");
        $response->assertSee("+49-123456789");
        $response->assertSee("https://something.asia");
        $response->assertSee("https://something.asia/some.jpg");
        $response->assertSee("Street");
        $response->assertSee("Locality");
        $response->assertSee("Country");
        $response->assertSee("123456");
    }
}
