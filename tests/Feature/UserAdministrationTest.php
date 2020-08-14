<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserAdministrationTest extends TestCase
{
    use DatabaseTransactions;

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
}
