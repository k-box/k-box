<?php

namespace Tests\Feature;

use KBox\User;
use KBox\Option;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IdentityControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function protected_routes_provider()
    {
        return [
            [Capability::$ADMIN, 'administration.identity.index', 'get', 200],
            [[Capability::MANAGE_KBOX], 'administration.identity.index', 'get', 200],
            [Capability::$PROJECT_MANAGER, 'administration.identity.index', 'get', 403],
            [Capability::$PARTNER, 'administration.identity.index', 'get', 403],
            [[Capability::RECEIVE_AND_SEE_SHARE], 'administration.identity.index', 'get', 403],
            [Capability::$ADMIN, 'administration.identity.store', 'post', 302],
            [[Capability::MANAGE_KBOX], 'administration.identity.store', 'post', 302],
            [Capability::$PROJECT_MANAGER, 'administration.identity.store', 'post', 403],
            [Capability::$PARTNER, 'administration.identity.store', 'post', 403],
            [[Capability::RECEIVE_AND_SEE_SHARE], 'administration.identity.store', 'post', 403],
        ];
    }

    /**
     * @dataProvider protected_routes_provider
     */
    public function testContactsControllerAuth($capabilities, $route, $method, $expected_status_code)
    {
        $user = tap(factory(User::class)->create(), function ($u) use ($capabilities) {
            $u->addCapabilities($capabilities);
        });
        
        $response = $this->actingAs($user)->{$method}(route($route));

        if ($expected_status_code === 403) {
            $response->assertStatus(200);
            $response->assertViewIs('errors.403');
        } else {
            $response->assertStatus($expected_status_code);
        }
    }

    public function testAdminDashboardNoticeShown()
    {
        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)->get(route('administration.index'));

        $response->assertSee(trans('notices.contacts_not_configured', ['url' => route('administration.identity.index')]));
    }
    
    public function testContactsAreSaved()
    {
        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)
                         ->from(route('administration.identity.index'))
                         ->post(route('administration.identity.store'), [
                            'name' => 'An Organization name',
                            'email' => 'some@email.com',
                            'phone' => '+49-123456789',
                            'website' => 'https://k-link.technology',
                            'image' => 'https://k-link.technology/some.jpg',
                            'address_street' => 'Street',
                            'address_locality' => 'Locality',
                            'address_country' => 'Country',
                            'address_zip' => '123456',
                         ]);

        $response->assertRedirect(route('administration.identity.index'));
        $response->assertSessionHas('flash_message', trans('administration.identity.contact_info_updated'));

        $contacts = Option::sectionAsArray('contact');

        $this->assertNotEmpty($contacts);

        $this->assertEquals([
            'contact' => [
                "name" => "An Organization name",
                "email" => "some@email.com",
                "phone" => "+49-123456789",
                "website" => "https://k-link.technology",
                "image" => "https://k-link.technology/some.jpg",
                "address_street" => "Street",
                "address_locality" => "Locality",
                "address_country" => "Country",
                "address_zip" => "123456",
            ]
        ], $contacts);

        $response = $this->actingAs($user)
                         ->get(route('administration.identity.index'));

        $response->assertSee("An Organization name");
        $response->assertSee("some@email.com");
        $response->assertSee("+49-123456789");
        $response->assertSee("https://k-link.technology");
        $response->assertSee("https://k-link.technology/some.jpg");
        $response->assertSee("Street");
        $response->assertSee("Locality");
        $response->assertSee("Country");
        $response->assertSee("123456");
    }

    public function testContactsAreSavedWithOnlyRequired()
    {

        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)
                         ->from(route('administration.identity.index'))
                         ->post(route('administration.identity.store'), [
                            'name' => 'An Organization name',
                            'email' => 'some@email.com',
                            'phone' => '',
                            'website' => '',
                            'image' => '',
                            'address_street' => '',
                            'address_locality' => '',
                            'address_country' => '',
                            'address_zip' => '',
                         ]);

        $response->assertLocation(route('administration.identity.index'));
        $response->assertSessionHas('flash_message', trans('administration.identity.contact_info_updated'));

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

        // make sure contact information are not set
        Option::where('key', 'LIKE', 'contact.%')->delete();

        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$ADMIN);
        });

        $response = $this->actingAs($user)
                         ->from(route('administration.identity.index'))
                         ->post(route('administration.identity.store'), [
                             'name' => '',
                             'email' => '',
                         ]);

        $response->assertRedirect(route('administration.identity.index'));
        $response->assertSessionHasErrors(['name', 'email']);
    }
}
