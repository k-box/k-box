<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use KBox\RoutingHelpers;
use KBox\DuplicateDocument;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DuplicateDocumentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sent_return_true_when_notification_sent_at_has_a_value()
    {
        $d = (new DuplicateDocument())->forceFill([
            'notification_sent_at' => Carbon::now()
        ]);

        $this->assertTrue($d->sent);
    }

    public function test_sent_attributes_can_set_the_notification_sent_at_timestamp()
    {
        $d = new DuplicateDocument();

        $d->sent = true;

        $this->assertNotNull($d->notification_sent_at);
    }

    public function test_message_attribute_is_retrievable()
    {
        $d = new DuplicateDocument();

        $this->assertEquals('', $d->message);
    }

    public function test_message_report_me_as_the_owner()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $duplicate = factory(DuplicateDocument::class)->create(['user_id' => $user->id]);

        $expected = trans('documents.duplicates.message_me_owner', [
            'duplicate_link' => RoutingHelpers::preview($duplicate->document),
            'duplicate_title' => $duplicate->document->title,
            'existing_link' => RoutingHelpers::preview($duplicate->duplicateOf),
            'existing_title' => $duplicate->duplicateOf->title,
        ]);

        $this->assertEquals($expected, $duplicate->message);
    }

    public function test_message_report_user_as_the_owner()
    {
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $duplicate = factory(DuplicateDocument::class)->create();

        $doc = $duplicate->document;
        $doc->owner_id = $user->id;
        $doc->save();

        $expected = trans('documents.duplicates.message_with_owner', [
            'duplicate_link' => RoutingHelpers::preview($duplicate->document),
            'duplicate_title' => $duplicate->document->title,
            'existing_link' => RoutingHelpers::preview($duplicate->duplicateOf),
            'existing_title' => $duplicate->duplicateOf->title,
            'owner' => $duplicate->duplicateOf->owner->name
        ]);

        $this->assertEquals($expected, $duplicate->message);
    }

    public function test_message_contains_collections()
    {
        $service = app('KBox\Documents\Services\DocumentsService');

        $this->withKlinkAdapterFake();
        
        $user = tap(factory(\KBox\User::class)->create(), function ($u) {
            $u->addCapabilities(Capability::$PARTNER);
        });

        $project = tap(factory(\KBox\Project::class)->create(), function ($p) use ($user) {
            $p->users()->attach($user->id);
        });

        $manager = $project->manager;

        $descriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $manager->id
        ]);
        $duplicateDescriptor = factory(\KBox\DocumentDescriptor::class)->create([
            'owner_id' => $user->id,
            'hash' => $descriptor->hash
        ]);

        $collection_root = $service->createGroup($manager, 'collection_level_one', null, null, true);
        $collection_level_one = $service->createGroup($manager, 'collection_level_one', null, $collection_root, true);

        $collection_level_one->documents()->save($descriptor);

        $service->addDocumentToGroup($manager, $descriptor, $project->collection);
        
        $duplicate = factory(DuplicateDocument::class)->create([
            'user_id' => $user->id,
            'duplicate_document_id' => $duplicateDescriptor->id,
            'document_id' => $descriptor->id,
        ]);

        $expected = trans('documents.duplicates.message_in_collection', [
            'duplicate_link' => RoutingHelpers::preview($duplicate->document),
            'duplicate_title' => e($duplicate->document->title),
            'existing_link' => RoutingHelpers::preview($duplicate->duplicateOf),
            'existing_title' => e($duplicate->duplicateOf->title),
            'owner' => e($manager->name),
            'collections' => '<a href="'.route('documents.groups.show', [ 'id' => $project->collection->id, 'highlight' => $descriptor->id]).'">'.$project->collection->name.'</a>',
        ]);

        $this->assertEquals($expected, $duplicate->message);
    }
}
