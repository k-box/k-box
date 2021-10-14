<?php

namespace Tests\Feature\Licenses;

use KBox\Option;
use Tests\TestCase;
use KBox\DocumentDescriptor;
use KBox\Jobs\ReindexDocument;
use OneOffTech\Licenses\License;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class EditDefaultLicenseTest extends TestCase
{
    use  WithoutMiddleware;

    public function test_default_license_is_required()
    {
        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/default', [
            'default_license' => ''
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');
        $response->assertSessionHasErrors('default_license');
        $this->assertFalse(Option::isDefaultLicenseConfigured());
    }
    
    public function test_license_is_a_string()
    {
        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/default', [
            'default_license' => ['an', 'array']
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');
        $response->assertSessionHasErrors('default_license');
        $this->assertFalse(Option::isDefaultLicenseConfigured());
    }

    public function test_invalid_license_is_selected()
    {
        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/default', [
            'default_license' => 'a-string'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');
        $response->assertSessionHasErrors();
        $this->assertFalse(Option::isDefaultLicenseConfigured());
    }

    public function test_not_available_license_is_used()
    {
        Option::put(Option::COPYRIGHT_AVAILABLE_LICENSES, '["PD", "C"]');
        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, null);

        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/default', [
            'default_license' => 'CC-BY-4.0'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');
        $response->assertSessionHasErrors();
        $this->assertFalse(Option::isDefaultLicenseConfigured());
    }
    
    public function test_default_license_is_saved()
    {
        Option::put(Option::COPYRIGHT_AVAILABLE_LICENSES, '["PD", "C", "CC-BY-4.0"]');
        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, null);

        $user = factory(\KBox\User::class)->state('admin')->create();

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/default', [
            'default_license' => 'CC-BY-4.0'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');

        $this->assertTrue(Option::isDefaultLicenseConfigured());

        $option = Option::copyright_default_license();

        $this->assertInstanceOf(License::class, $option);
        $this->assertEquals('CC-BY-4.0', $option->id);
    }

    public function test_default_license_is_applied_to_documents_without_a_license()
    {
        Queue::fake();
        
        Option::put(Option::COPYRIGHT_AVAILABLE_LICENSES, '["PD", "C", "CC-BY-4.0"]');
        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, null);

        $user = factory(\KBox\User::class)->state('admin')->create();
        $documents = factory(\KBox\DocumentDescriptor::class, 3)->create([
            'copyright_usage' => null,
        ]);
        $document_ids = $documents->pluck('id')->toArray();

        $this->assertEquals(3, DocumentDescriptor::whereIn('id', $documents->pluck('id')->toArray())->whereNull('copyright_usage')->count());

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/default', [
            'default_license' => 'CC-BY-4.0',
            'apply_to' => 'previous'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');

        $this->assertTrue(Option::isDefaultLicenseConfigured());

        $this->assertEquals(0, DocumentDescriptor::whereIn('id', $document_ids)->whereNull('copyright_usage')->count());

        Queue::assertPushed(ReindexDocument::class, function ($job) use ($document_ids) {
            return in_array($job->document->id, $document_ids);
        });
    }

    public function test_default_license_is_applied_to_all_documents()
    {
        Queue::fake();
        
        Option::put(Option::COPYRIGHT_AVAILABLE_LICENSES, '["PD", "C", "CC-BY-4.0"]');
        Option::put(Option::COPYRIGHT_DEFAULT_LICENSE, null);

        $user = factory(\KBox\User::class)->state('admin')->create();
        $documents_without_license = factory(\KBox\DocumentDescriptor::class, 3)->create([
            'copyright_usage' => null,
        ]);
        $documents_with_license = factory(\KBox\DocumentDescriptor::class, 3)->create([
            'copyright_usage' => 'C',
        ]);
        $document_ids = $documents_without_license->merge($documents_with_license)->pluck('id')->toArray();

        $this->assertCount(6, $document_ids);

        $response = $this->actingAs($user)->from('/administration/licenses')->put('/administration/licenses/default', [
            'default_license' => 'CC-BY-4.0',
            'apply_to' => 'all'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/administration/licenses');

        $this->assertTrue(Option::isDefaultLicenseConfigured());

        $this->assertEquals(6, DocumentDescriptor::whereIn('id', $document_ids)->where('copyright_usage', 'CC-BY-4.0')->count());

        Queue::assertPushed(ReindexDocument::class, function ($job) use ($document_ids) {
            return in_array($job->document->id, $document_ids);
        });
    }
}
