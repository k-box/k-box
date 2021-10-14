<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;

use Illuminate\Support\Facades\Notification;
use KBox\Notifications\ResetPasswordNotification;

class UserAdministrationTest extends TestCase
{
    public function test_admin_can_retrieve_users()
    {
        $user = factory(User::class)->states('admin')->create();
        
        $response = $this->actingAs($user)
            ->get(route('administration.users.index'));
        
        $response->assertViewIs('administration.users');

        $response->assertViewHas('users', User::withTrashed()->get());
        $response->assertViewHas('pagetitle', trans('administration.menu.accounts'));
        $response->assertViewHas('current_user', $user->getKey());
    }

    public function test_non_admin_cannot_retrieve_users()
    {
        $user = factory(User::class)->create();
        
        $response = $this->actingAs($user)
                    ->get(route('administration.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_disable_users()
    {
        $user = factory(User::class)->states('admin')->create();
        $userToDisable = factory(User::class)->create();
        
        $response = $this->actingAs($user)
            ->from(route('administration.users.index'))
            ->delete(route('administration.users.destroy', ['user' => $userToDisable->getKey()]));
        
        $response->assertRedirect(route('administration.users.index'));

        $response->assertSessionHas('flash_message', trans('administration.accounts.disabled_msg', ['name' => $userToDisable->name]));
        $this->assertTrue($userToDisable->fresh()->trashed());
    }

    public function test_admin_can_activate_a_disabled_user()
    {
        $user = factory(User::class)->states('admin')->create();
        $userToRestore = factory(User::class)->create(['deleted_at' => now()]);
        
        $response = $this->actingAs($user)
            ->from(route('administration.users.index'))
            ->get(route('administration.users.restore', ['id' => $userToRestore->getKey()]));
        
        $response->assertRedirect(route('administration.users.index'));

        $response->assertSessionHas('flash_message', trans('administration.accounts.enabled_msg', ['name' => $userToRestore->name]));
        $this->assertFalse($userToRestore->fresh()->trashed());
    }

    public function test_restore_available_only_to_admin_users()
    {
        $user = factory(User::class)->create();
        $userToRestore = factory(User::class)->create(['deleted_at' => now()]);
        
        $response = $this->actingAs($user)
            ->from(route('administration.users.index'))
            ->get(route('administration.users.restore', ['id' => $userToRestore->getKey()]));
        
        $response->assertForbidden();
    }

    public function test_user_cannot_disable_itself()
    {
        $user = factory(User::class)->states('admin')->create();
        
        $response = $this->actingAs($user)
            ->from(route('administration.users.index'))
            ->delete(route('administration.users.destroy', ['user' => $user->getKey()]));
        
        $response->assertRedirect(route('administration.users.index'));
        $response->assertSessionHasErrors('user');
        $this->assertFalse($user->fresh()->trashed());
    }

    public function test_non_admin_cannot_disable_users()
    {
        $user = factory(User::class)->create();
        $userToDisable = factory(User::class)->create();
        
        $response = $this->actingAs($user)
            ->from(route('administration.users.index'))
            ->delete(route('administration.users.destroy', ['user' => $userToDisable->getKey()]));

        $response->assertForbidden();
    }

    public function test_admin_can_see_user()
    {
        $user = factory(User::class)->states('admin')->create();
        $account = factory(User::class)->create();
        
        $response = $this->actingAs($user)
            ->get(route('administration.users.show', ['user' => $account->getKey()]));
        
        $response->assertViewIs('administration.users.edit');

        $response->assertViewHas('user', $account);
        $response->assertViewHas('pagetitle', trans('administration.accounts.edit_account_title', ['name' => $account->name]));
    }

    public function test_admin_can_trigger_password_reset_requests()
    {
        Notification::fake();

        $user = factory(User::class)->states('admin')->create();
        $userToReset = factory(User::class)->create();
        
        $response = $this->actingAs($user)
            ->from(route('administration.users.index'))
            ->get(route('administration.users.resetpassword', ['id' => $userToReset->getKey()]));
        
        $response->assertRedirect(route('administration.users.index'));
        $response->assertSessionHas('flash_message', trans('administration.accounts.reset_sent', ['name' => $userToReset->name, 'email' => $userToReset->email]));

        Notification::assertSentTo($userToReset, ResetPasswordNotification::class);
    }
    
    public function test_oly_admin_allowed_to_trigger_password_reset_requests()
    {
        $user = factory(User::class)->create();
        $userToReset = factory(User::class)->create();
        
        $response = $this->actingAs($user)
            ->from(route('administration.users.index'))
            ->get(route('administration.users.resetpassword', ['id' => $userToReset->getKey()]));
        
        $response->assertForbidden();
    }
}
