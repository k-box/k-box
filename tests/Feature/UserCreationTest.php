<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Notification;
use KBox\User;
use KBox\Notifications\UserCreatedNotification;

/**
 * Tests the UserAdministrationController::create and store
*/
class UserCreationTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    private function validParams($params)
    {
        return array_merge([
            'email' => 'test@klink.asia',
            'name' => 'Test User',
            'capabilities' => ['receive_share'],
        ], $params);
    }
    

    public function test_user_is_created()
    {
        $user = factory('KBox\User')->create();

        Notification::fake();

        $response = $this->actingAs($user)
                    ->post(route('administration.users.store'), $this->validParams([]));

        $response->assertRedirect(route('administration.users.index'));

        $user_created = User::where('email', 'test@klink.asia')->first();

        $this->assertNotNull($user_created);
        $this->assertEquals('test@klink.asia', $user_created->email);
        $this->assertEquals('Test User', $user_created->name);
        $this->assertNotEmpty($user_created->password);
        $this->assertTrue($user_created->can_all_capabilities(['receive_share']));

        Notification::assertSentTo(
            [$user_created], UserCreatedNotification::class
        );
    }

    public function test_wrong_email_is_rejected()
    {
        $user = factory('KBox\User')->create();

        $response = $this->actingAs($user)
                    ->from('/administration/users/create')
                    ->post(route('administration.users.store'), $this->validParams([
                        'email' => 'testklink.asia'
                    ]));

        $response->assertRedirect('/administration/users/create');
        $response->assertSessionHasErrors('email');
        $this->assertNull(User::where('email', 'test@klink.asia')->first());
    }

    public function test_empty_user_name_is_rejected()
    {
        $user = factory('KBox\User')->create();

        $response = $this->actingAs($user)
                    ->from('/administration/users/create')
                    ->post(route('administration.users.store'), $this->validParams([
                        'name' => ''
                    ]));

        $response->assertRedirect('/administration/users/create');
        $response->assertSessionHasErrors('name');
        $this->assertNull(User::where('email', 'test@klink.asia')->first());
    }

    public function test_empty_capability_is_rejected()
    {
        $user = factory('KBox\User')->create();

        $response = $this->actingAs($user)
                    ->from('/administration/users/create')
                    ->post(route('administration.users.store'), $this->validParams([
                        'capabilities' => []
                    ]));

        $response->assertRedirect('/administration/users/create');
        $response->assertSessionHasErrors('capabilities');
        $this->assertNull(User::where('email', 'test@klink.asia')->first());
    }
}
