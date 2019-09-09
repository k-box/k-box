<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\File;
use KBox\Services\Quota;
use KBox\User;

class UserQuotaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_current_used_quota_is_calculated_for_user()
    {
        $user = tap(factory(User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $file = factory(File::class)->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'size' => 1000,
        ]);
        
        $doc = factory(DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'file_id' => $file->id,
        ]);

        $used_quota = Quota::used($user);

        $this->assertEquals(100, $used_quota);
    }
}
