<?php

use KlinkDMS\User;
use KlinkDMS\Capability;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use KlinkDMS\Notifications\UserCreatedNotification;

/**
 * Tests the UserAdministrationController
*/
class UserAdministrationControllerTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function capabilities()
    {
        return [
            
            [Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER],
            [Capability::$PARTNER],
        ];
    }
    

    public function testUserCreate()
    {
        $user = $this->createAdminUser();
        $institution_id = $user->institution_id;

        $this->actingAs($user);

        Notification::fake();

        $this->visit(route('administration.users.create'));

        $this->type('test@klink.asia', 'email');
        $this->type('Test User', 'name');
        $this->select($institution_id, 'institution');

        $this->storeInput('capabilities[]', ['receive_share']);

        $this->press(trans('administration.accounts.labels.create'));

        $user_created = User::where('email', 'test@klink.asia')->first();

        $this->assertNotNull($user_created);

        Notification::assertSentTo(
            [$user_created], UserCreatedNotification::class
        );
    }
}
