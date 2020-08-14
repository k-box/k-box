<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests the UserAdministrationController::edit and update
*/
class UserEditTest extends TestCase
{
    use DatabaseTransactions;

    private function validUpdateParams(User $user, $params)
    {
        return array_merge([
            'email' => 'test@k-link.technology',
            'name' => 'Test User',
            'password' => '',
            'send_password' => '1',
            'capabilities' => Capability::$PARTNER,
        ], $params);
    }

    public function test_admin_can_edit_user()
    {
        $user = factory(User::class)->states('admin')->create();
        $account = factory(User::class)->create();
        
        $response = $this->actingAs($user)
            ->get(route('administration.users.edit', ['user' => $account->getKey()]));
        
        $response->assertViewHas('user', $account);
        $response->assertViewIs('administration.users.edit');

        $response->assertViewHas('pagetitle', trans('administration.accounts.edit_account_title', ['name' => $account->name]));
        $response->assertViewHas('edit_enabled', true);
        $response->assertSee(trans('administration.accounts.labels.perms'));
    }

    public function test_admin_cannot_edit_own_permission()
    {
        $user = factory(User::class)->states('admin')->create();
        
        $response = $this->actingAs($user)
            ->get(route('administration.users.edit', ['user' => $user->getKey()]));
        
        $response->assertViewIs('administration.users.edit');

        $response->assertViewHas('user', $user);
        $response->assertViewHas('pagetitle', trans('administration.accounts.edit_account_title', ['name' => $user->name]));
        $response->assertViewHas('edit_enabled', false);
        $response->assertDontSee(trans('administration.accounts.labels.perms'));
    }
    
    public function test_user_name_updated()
    {
        $user = factory(User::class)->states('admin')->create();
        $account = factory(User::class)->create();

        $response = $this->actingAs($user)
                    ->from(route('administration.users.edit', ['user' => $account->getKey()]))
                    ->put(
                        route('administration.users.update', ['user' => $account->getKey()]),
                        ['name' => 'changed name',
                        'email' => $account->email]
                    );
        
        $response->assertRedirect(route('administration.users.show', ['user' => $account->getKey()]));
        $response->assertSessionHas('flash_message', trans('administration.accounts.updated_msg'));

        $updated_account = $account->fresh();

        $this->assertNotNull($updated_account);
        $this->assertEquals($account->email, $updated_account->email);
        $this->assertEquals('changed name', $updated_account->name);
        $this->assertNotEmpty($updated_account->password);
        $this->assertEquals($account->capabilities, $updated_account->capabilities);
    }
    
    public function test_user_email_updated()
    {
        $user = factory(User::class)->states('admin')->create();
        $account = factory(User::class)->create();

        $response = $this->actingAs($user)
                    ->from(route('administration.users.edit', ['user' => $account->getKey()]))
                    ->put(
                        route('administration.users.update', ['user' => $account->getKey()]),
                        ['name' => $account->name,
                            'email' => 'hello@new.test']
                    );
        
        $response->assertRedirect(route('administration.users.show', ['user' => $account->getKey()]));
        $response->assertSessionHas('flash_message', trans('administration.accounts.updated_msg'));

        $updated_account = $account->fresh();

        $this->assertNotNull($updated_account);
        $this->assertEquals('hello@new.test', $updated_account->email);
        $this->assertEquals($account->name, $updated_account->name);
        $this->assertNotEmpty($updated_account->password);
        $this->assertEquals($account->capabilities, $updated_account->capabilities);
    }
    
    public function test_user_not_updated_if_email_already_taken()
    {
        $user = factory(User::class)->states('admin')->create();
        $account = factory(User::class)->create();

        $response = $this->actingAs($user)
                    ->from(route('administration.users.edit', ['user' => $account->getKey()]))
                    ->put(
                        route('administration.users.update', ['user' => $account->getKey()]),
                        ['name' => $account->name,'email' => $user->email]
                    );
        
        $response->assertRedirect(route('administration.users.edit', ['user' => $account->getKey()]));
        $response->assertSessionHasErrors('email');

        $updated_account = $account->fresh();

        $this->assertNotNull($updated_account);
        $this->assertEquals($account->email, $updated_account->email);
        $this->assertEquals($account->name, $updated_account->name);
        $this->assertNotEmpty($updated_account->password);
        $this->assertEquals($account->capabilities, $updated_account->capabilities);
    }
}
