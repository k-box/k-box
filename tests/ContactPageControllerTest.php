<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Option;

class ContactPageControllerTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function testContactShowsFakeDataIfContactsAreNotConfigured()
    {
        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $this->visit(route('contact'));

        $this->assertViewName('static.contact');

        $this->see('K-Link');
        $this->see('info@klink.asia');
        $this->see('https://klink.asia');
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
        
        $this->visit(route('contact'));

        $this->assertViewName('static.contact');

        $this->see("An Organization name");
        $this->see("some@email.com");
        $this->see("+49-123456789");
        $this->see("https://something.asia");
        $this->see("https://something.asia/some.jpg");
        $this->see("Street");
        $this->see("Locality");
        $this->see("Country");
        $this->see("123456");
    }
}
