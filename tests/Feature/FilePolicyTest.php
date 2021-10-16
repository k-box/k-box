<?php

namespace Tests\Feature;

use KBox\File;
use KBox\User;
use Tests\TestCase;
use KBox\Policies\FilePolicy;
use Illuminate\Foundation\Testing\WithFaker;

class FilePolicyTest extends TestCase
{
    use  WithFaker;

    public function test_deny_see_username()
    {
        $user = User::factory()->partner()->create();

        $file = File::factory()->create();

        $can = (new FilePolicy())->see_uploader($user, $file);

        $this->assertFalse($can);
    }
    
    public function test_uploader_not_visible_if_user_trashed()
    {
        $file = File::factory()->create();
        $user = $file->user;

        $file->user->delete();

        $can = (new FilePolicy())->see_uploader($file->user, $file);

        $this->assertFalse($can);
    }
    
    public function test_file_uploader_can_be_seen()
    {
        $file = File::factory()->create();

        $can = (new FilePolicy())->see_uploader($file->user, $file);

        $this->assertTrue($can);
    }
}
