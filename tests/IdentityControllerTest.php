<?php

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use KlinkDMS\Option;
use KlinkDMS\Capability;

class IdentityControllerTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function protected_routes_provider()
    {
        return [
            [Capability::$ADMIN, 'administration.identity.index', 'get', 200],
            [Capability::$DMS_MASTER, 'administration.identity.index', 'get', 200],
            [Capability::$PROJECT_MANAGER, 'administration.identity.index', 'get', 403],
            [Capability::$PARTNER, 'administration.identity.index', 'get', 403],
            [Capability::$GUEST, 'administration.identity.index', 'get', 403],
            [Capability::$ADMIN, 'administration.identity.store', 'post', 302],
            [Capability::$DMS_MASTER, 'administration.identity.store', 'post', 302],
            [Capability::$PROJECT_MANAGER, 'administration.identity.store', 'post', 403],
            [Capability::$PARTNER, 'administration.identity.store', 'post', 403],
            [Capability::$GUEST, 'administration.identity.store', 'post', 403],
        ];
    }

    /**
     * @dataProvider protected_routes_provider
     */
    public function testContactsControllerAuth($capabilities, $route, $method, $expected_status_code)
    {
        $user = $this->createUser($capabilities);
        
        $this->actingAs($user);
        
        $this->{$method}(route($route));

        if ($expected_status_code === 403) {
            $this->assertResponseStatus(200);
            $this->assertViewName('errors.403');
        } else {
            $this->assertResponseStatus($expected_status_code);
        }
    }

    public function testAdminDashboardNoticeShown()
    {
        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.index'));

        $this->see(trans('notices.contacts_not_configured', ['url' => route('administration.identity.index')]));
    }
    
    public function testContactsAreSaved()
    {
        $institution = factory('KlinkDMS\Institution')->create([
            'klink_id' => \Config::get('dms.institutionID')
        ]);

        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.identity.index'));

        $this->type('An Organization name', 'name');
        $this->type('some@email.com', 'email');
        $this->type('+49-123456789', 'phone');
        $this->type('https://klink.asia', 'website');
        $this->type('https://klink.asia/some.jpg', 'image');
        $this->type('Street', 'address_street');
        $this->type('Locality', 'address_locality');
        $this->type('Country', 'address_country');
        $this->type('123456', 'address_zip');

        $this->press(trans('administration.settings.save_btn'));

        $this->seePageIs(route('administration.identity.index'));
        $this->see(trans('administration.identity.contact_info_updated'));

        $contacts = Option::sectionAsArray('contact');

        $this->assertNotEmpty($contacts);

        $this->assertEquals([
            'contact' => [
                "name" => "An Organization name",
                "email" => "some@email.com",
                "phone" => "+49-123456789",
                "website" => "https://klink.asia",
                "image" => "https://klink.asia/some.jpg",
                "address_street" => "Street",
                "address_locality" => "Locality",
                "address_country" => "Country",
                "address_zip" => "123456",
            ]
        ], $contacts);

        $this->see("An Organization name");
        $this->see("some@email.com");
        $this->see("+49-123456789");
        $this->see("https://klink.asia");
        $this->see("https://klink.asia/some.jpg");
        $this->see("Street");
        $this->see("Locality");
        $this->see("Country");
        $this->see("123456");
    }

    public function testContactsAreSavedWithOnlyRequired()
    {
        $institution = factory('KlinkDMS\Institution')->create([
            'klink_id' => \Config::get('dms.institutionID')
        ]);

        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.identity.index'));

        $this->type('An Organization name', 'name');
        $this->type('some@email.com', 'email');
        $this->type('', 'phone');
        $this->type('', 'website');
        $this->type('', 'image');
        $this->type('', 'address_street');
        $this->type('', 'address_locality');
        $this->type('', 'address_country');
        $this->type('', 'address_zip');

        $this->press(trans('administration.settings.save_btn'));

        $this->seePageIs(route('administration.identity.index'));
        $this->see(trans('administration.identity.contact_info_updated'));

        $contacts = Option::sectionAsArray('contact');

        $this->assertNotEmpty($contacts);

        $this->assertEquals([
            'contact' => [
                "name" => "An Organization name",
                "email" => "some@email.com",
                "phone" => "",
                "website" => "",
                "image" => "",
                "address_street" => "",
                "address_locality" => "",
                "address_country" => "",
                "address_zip" => "",
            ]
        ], $contacts);
    }
    public function testContactsRequiredValidation()
    {
        $institution = factory('KlinkDMS\Institution')->create([
            'klink_id' => \Config::get('dms.institutionID')
        ]);

        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = $this->createAdminUser();

        $this->actingAs($user);

        $this->visit(route('administration.identity.index'));

        $this->type('', 'name');
        $this->type('', 'email');

        $this->press(trans('administration.settings.save_btn'));

        $this->seePageIs(route('administration.identity.index'));
        $this->see('The name field is required');
        $this->see('The email field is required');
    }
}
