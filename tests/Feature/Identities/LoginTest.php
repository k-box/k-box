<?php

namespace Tests\Feature\Identities;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_social_login_forbidden_if_registration_is_disabled()
    {
        config(['registration.enable' => false]);

        $response = $this->get(route('oneofftech::login.provider', ['provider' => 'gitlab']));

        $response->assertForbidden();
    }
    
    public function test_social_option_not_visible_when_disabled()
    {
        config([
            'registration.enable' => true,
            'identities.providers' => null,
        ]);

        $response = $this->get(route('login'));

        $response->assertOk();

        $response->assertDontSee('Log in via Gitlab');
    }
    
    public function test_social_login_presented_if_enabled()
    {
        config([
            'registration.enable' => true,
            'identities.providers' => 'gitlab,dropbox',
        ]);

        $response = $this->get(route('login'));

        $response->assertOk();

        $response->assertSee('Log in via Gitlab');
        $response->assertSee('Log in via Dropbox');
    }
}
