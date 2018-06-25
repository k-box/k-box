<?php

namespace Tests\Feature;

use KBox\User;
use Carbon\Carbon;
use Tests\TestCase;
use KBox\Capability;
use Klink\DmsAdapter\KlinkSearchRequest;
use Klink\DmsAdapter\KlinkSearchResults;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RecentDocumentsTest extends TestCase
{
    use DatabaseTransactions, WithoutMiddleware;

    public function recent_date_range_provider()
    {
        return [
            ['today'],
            ['yesterday'],
            ['currentweek'],
            ['currentmonth'],
        ];
    }

    public function recent_items_per_page_provider()
    {
        return [
            [5],
            [10],
            [15],
            [25],
            [50],
        ];
    }

    public function recent_sorting_provider()
    {
        return [
            ['a', 'ASC'],
            ['d', 'DESC'],
        ];
    }

    public function test_shared_is_listed_as_recent_even_if_user_has_no_personal_documents()
    {
        $adapter = $this->withKlinkAdapterFake();
        
        $user_sender = factory('KBox\User')->create();
        $user_receiver = factory('KBox\User')->create();

        $descriptor = factory('KBox\DocumentDescriptor')->create();

        $share = $descriptor->shares()->create([
            'user_id' => $user_sender->id,
            'sharedwith_id' => $user_receiver->id,
            'sharedwith_type' => get_class($user_receiver),
            'token' => 'share-token',
        ]);

        $response = $this->actingAs($user_receiver)->get(route('documents.recent'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('documents');

        $original_response = $response->getOriginalContent();
        $listed_documents = $original_response['documents']->values()->collapse();
        $this->assertEquals(1, $listed_documents->count());
        
        $recent = $listed_documents->first();
        $this->assertEquals($descriptor->id, $recent->id);
    }

    /**
     * @dataProvider recent_date_range_provider
     */
    public function test_range_can_be_specified($range)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $url = route('documents.recent', ['range' => $range]);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('range', $range);

        $user = $user->fresh();
        $this->assertEquals($range, $user->optionRecentRange());
    }
    
    /**
     * Test Items per page option is honored
     *
     * @dataProvider recent_items_per_page_provider
     */
    public function test_recent_shows_expected_items_per_page($items_per_page = 5)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        $this->actingAs($user);

        $documents = $this->createRecentDocuments($items_per_page+1, $user);

        $url = route('documents.recent').'?n='.$items_per_page ;

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        
        $user = $user->fresh();
        $this->assertEquals($items_per_page, $user->optionItemsPerPage());

        $original_response = $response->getOriginalContent();
        $pagination = $original_response['pagination'];
        
        $listed_documents = $original_response['documents']->values()->collapse();
        $this->assertEquals($items_per_page, $listed_documents->count());
        $this->assertEquals($documents->count(), $pagination->total());
        $this->assertEquals(2, $pagination->lastPage());
    }

    public function test_number_of_items_per_page_must_be_positive_and_above_zero()
    {
        $items_per_page = 0;
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $url = route('documents.recent').'?n='.$items_per_page ;

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        
        $user = $user->fresh();
        $this->assertEquals(12, $user->optionItemsPerPage());

        $items_per_page = -1;
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $url = route('documents.recent').'?n='.$items_per_page ;

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        
        $user = $user->fresh();
        $this->assertEquals(12, $user->optionItemsPerPage());
    }
    
    /**
     * Test sorting option is honored
     *
     * @dataProvider recent_sorting_provider
     */
    public function test_sorting_is_supported($option, $expected_value)
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $created_at = Carbon::now();

        $recent_documents = [
            $this->createRecentDocument($user, $created_at),
            $this->createRecentDocument($user, $created_at->copy()->subMinutes(10)),
            $this->createRecentDocument($user, $created_at->copy()->subHours(2)),
        ];

        $url = route('documents.recent').'?o='.$option ;

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('order', $expected_value);

        $original_response = $response->getOriginalContent();
        $listed_documents = $original_response['documents']->values()->collapse();
        $this->assertEquals(3, $listed_documents->count());

        if ($expected_value === 'ASC') {
            $this->assertEquals([
                $recent_documents[2]->id,
                $recent_documents[1]->id,
                $recent_documents[0]->id,
            ], $listed_documents->pluck('id')->toArray());
        } elseif ($expected_value === 'DESC') {
            $this->assertEquals([
                $recent_documents[0]->id,
                $recent_documents[1]->id,
                $recent_documents[2]->id,
            ], $listed_documents->pluck('id')->toArray());
        } else {
            $this->fail('Document sorting is not respecting ASC or DESC');
        }
    }
    
    /**
     * Test recent page contains expected documents
     *
     * - last updated by user
     * - last shared with me
     */
    public function test_recent_shows_expected_documents($range = 'today')
    {
        $this->withKlinkAdapterFake();

        $user = $this->createUser(Capability::$PARTNER);
        $user2 = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $documents = collect(); // documents created by $user
        $documents_user2 = collect();  // documents created by $user2

        $count_documents_by_me = 5;
        $count_documents_shared_with_me = 1;
        $count_documents_in_project = 1;

        // create some documents for $user
        for ($i=0; $i < $count_documents_by_me; $i++) {
            $documents->push($this->createDocument($user));
        }
        
        $doc = null;
        
        // create a project using $user2, add 1 document in the project
        $project1 = factory('KBox\Project')->create(['user_id' => $user2->id]);
        $project1->users()->attach($user->id);
        $doc = $this->createDocument($user2);
        $service = app('Klink\DmsDocuments\DocumentsService');
        $service->addDocumentToGroup($user2, $doc, $project1->collection);
        $doc = $doc->fresh();
        $documents_user2->push($doc);

        // create a second user, share with the first one a couple of documents
        for ($i=0; $i < $count_documents_shared_with_me; $i++) {
            $doc = $this->createDocument($user2);

            $doc->shares()->create([
                        'user_id' => $user2->id,
                        'sharedwith_id' => $user->id, //the id
                        'sharedwith_type' => get_class($user), //the class
                        'token' => hash('sha512', $doc->id),
                    ]);
            $documents_user2->push($doc);
        }

        // grab the last from $documents and change its updated_at to yesterday
        // (wrt the selected $range)
        $last = $documents->last();

        $last->updated_at = Carbon::yesterday();

        $last->timestamps = false; //temporarly disable the automatic upgrade of the updated_at field

        $last->save();

        $url = route('documents.recent', ['range' => $range]);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('range', $range);

        $listed_documents = $response->data('documents')->values()->collapse();

        $this->assertEquals(($count_documents_by_me - 1) + $count_documents_shared_with_me + $count_documents_in_project,
            $listed_documents->count());
    }

    public function test_recent_support_search_parameters()
    {
        $docs = factory('KBox\DocumentDescriptor', 10)->create();

        $adapter = $this->withKlinkAdapterFake();

        // prepare the request
        $searchRequest = KlinkSearchRequest::build('*', 'private', 1, 1, [], []);
        
        // prepare some fake results
        $adapter->setSearchResults('private', KlinkSearchResults::fake($searchRequest, []));

        $user = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        
        $url = route('documents.recent').'?s=hello';

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('search_replica_parameters', ['s' => 'hello']);

        $response->assertSee(e(route('documents.recent', ['range' => 'currentweek', 'n' => 12, 's' => 'hello'])));
        $response->assertSee(e(route('documents.recent', ['range' => 'currentweek', 'n' => 24, 's' => 'hello'])));
        $response->assertSee(e(route('documents.recent', ['range' => 'currentweek', 'n' => 50, 's' => 'hello'])));
        
        $response->assertSee(route('documents.recent', ['range' => 'today', 's' => 'hello']));
        $response->assertSee(route('documents.recent', ['range' => 'yesterday', 's' => 'hello']));
        $response->assertSee(route('documents.recent', ['range' => 'currentweek', 's' => 'hello']));
        $response->assertSee(route('documents.recent', ['range' => 'currentmonth', 's' => 'hello']));

        $response->assertSee('search-form');
    }

    public function test_recent_includes_documents_with_new_file_version()
    {
        $this->disableExceptionHandling();

        $adapter = $this->withKlinkAdapterFake();

        
        $user = factory('KBox\User')->create();

        $descriptor = factory('KBox\DocumentDescriptor')->create([
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
            'owner_id' => $user->id
        ]);

        $current_file_version = $descriptor->file;

        $new_file_version = factory('KBox\File')->create([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'revision_of' => $current_file_version->id,
            'user_id' => $user->id
        ]);

        $descriptor->file_id = $new_file_version->id;

        $descriptor->save();

        $response = $this->actingAs($user)->get(route('documents.recent').'/today');

        //assert recent for today shows the document

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('documents');

        $original_response = $response->getOriginalContent();
        $listed_documents = $original_response['documents']->values()->collapse();
        $this->assertEquals(1, $listed_documents->count());
        
        $recent = $listed_documents->first();
        $this->assertEquals($descriptor->id, $recent->id);
        $this->assertEquals($new_file_version->id, $recent->file->id);
    }

    public function test_recent_includes_documents_in_project_i_have_access()
    {
        $this->disableExceptionHandling();
        $adapter = $this->withKlinkAdapterFake();

        $manager = tap(factory(\KBox\User::class)->create())->addCapabilities(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);
        $user = tap(factory(\KBox\User::class)->create())->addCapabilities(Capability::$PARTNER);

        
        $project = factory('KBox\Project')->create([
            'user_id' => $manager->id
        ]);

        $project->users()->attach($user->id);

        $descriptor = factory('KBox\DocumentDescriptor')->create([
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2),
            'owner_id' => $manager->id
        ]);

        $project->collection->documents()->attach($descriptor->id);

        $current_file_version = $descriptor->file;

        $new_file_version = factory('KBox\File')->create([
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'revision_of' => $current_file_version->id,
            'user_id' => $user->id
        ]);

        $descriptor->file_id = $new_file_version->id;

        $descriptor->save();

        $response = $this->actingAs($user)->get(route('documents.recent').'/today');

        //assert recent for today shows the document

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('documents');

        $original_response = $response->getOriginalContent();
        $listed_documents = $original_response['documents']->values()->collapse();
        $this->assertEquals(1, $listed_documents->count());
        
        $recent = $listed_documents->first();
        $this->assertEquals($descriptor->id, $recent->id);
        $this->assertEquals($new_file_version->id, $recent->file->id);
    }

    public function test_recent_shows_partner_expected_documents_for_today()
    {
        $this->disableExceptionHandling();
        $this->withKlinkAdapterFake();

        // create a project with 2 members + the manager
        $target_user = $this->createUser(Capability::$PARTNER);

        list($today_docs, $yesterday_docs, $seven_days_docs, $thirty_days_docs) = $this->createRecentEnvironment($target_user);

        // today range

        $range = 'today';
        $items_per_page = 12;

        $url = route('documents.recent', ['range' => $range, 'n' => $items_per_page]);

        $response = $this->actingAs($target_user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('range', $range);

        $pagination = $response->data('pagination');
        $listed_documents = $response->data('documents')->values()->collapse();

        $this->assertEquals($today_docs->count(), $listed_documents->count());
        $this->assertEquals($today_docs->count(), $pagination->total());
        $this->assertEquals(1, $pagination->lastPage());
        $this->assertEquals($today_docs->pluck('id')->all(), $listed_documents->pluck('id')->all());
    }

    public function test_recent_shows_partner_expected_documents_in_the_current_month()
    {
        $this->disableExceptionHandling();
        $this->withKlinkAdapterFake();

        // create a project with 2 members + the manager
        $target_user = $this->createUser(Capability::$PARTNER);

        list($today_docs, $yesterday_docs, $seven_days_docs, $thirty_days_docs) = $this->createRecentEnvironment($target_user);

        // today range

        $range = 'currentmonth';
        $items_per_page = 50;

        $url = route('documents.recent', ['range' => $range, 'n' => $items_per_page]);

        $response = $this->actingAs($target_user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.recent');
        $response->assertViewHas('range', $range);

        $pagination = $response->data('pagination');
        $listed_documents = $response->data('documents')->values()->collapse();

        $this->assertEquals(25, $listed_documents->count());
        $this->assertEquals(25, $pagination->total());
        $this->assertEquals(1, $pagination->lastPage());

        $all_docs = $today_docs->merge($yesterday_docs)
            ->merge($seven_days_docs)
            ->merge($thirty_days_docs)->pluck('id')->all();

        $this->assertEquals($all_docs, $listed_documents->pluck('id')->all());
    }

    private function createUser($capabilities, $userParams = [])
    {
        return tap(factory(\KBox\User::class)->create($userParams))->addCapabilities($capabilities);
    }

    protected function createDocument(User $user, $visibility = 'private')
    {
        return factory('KBox\DocumentDescriptor')->create([
            'owner_id' => $user->id,
            'is_public' => $visibility === 'private' ? false : true,
        ]);
    }

    protected function createRecentDocuments($count, User $user, Carbon $date = null, $documentParams = [])
    {
        return factory('KBox\DocumentDescriptor', $count)->create(array_merge([
            'owner_id' => $user->id,
            'created_at' => $date ?? Carbon::now(),
            'updated_at' => $date ?? Carbon::now(),
        ], $documentParams));
    }

    protected function createRecentDocument(User $user, Carbon $date = null, $documentParams = [])
    {
        return factory('KBox\DocumentDescriptor')->create(array_merge([
            'owner_id' => $user->id,
            'created_at' => $date ?? Carbon::now(),
            'updated_at' => $date ?? Carbon::now(),
        ], $documentParams));
    }

    protected function createRecentEnvironment(User $target_user)
    {
        // $target_user = $this->createUser(Capability::$PARTNER);
        $member_user = $this->createUser(Capability::$PARTNER);
        $manager = $this->createUser(Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH);

        $project = factory('KBox\Project')->create([
            'user_id' => $manager->id
        ]);

        $project->users()->attach($target_user->id);
        $project->users()->attach($member_user->id);

        // add two first level collections and a second level collection to the project
        // each one created by a different user
        $service = app('Klink\DmsDocuments\DocumentsService');

        $collection_level_one = $service->createGroup($manager, 'collection_level_one', null, $project->collection, false);
        $collection_level_two = $service->createGroup($member_user, 'collection_level_two', null, $project->collection, false);
        $collection_level_three = $service->createGroup($target_user, 'collection_level_three', null, $collection_level_one, false);

        // create documents with various updated_at dates and add them to the collections
        $base_now = Carbon::now();
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $previous_monday = $today->copy()->previous(Carbon::MONDAY);
        $seven_days = $base_now->copy()->subDays(7);
        $thirty_days = $today->copy()->subMonth();

        $today_docs = $this->createRecentDocuments(5, $manager, $today);
        $yesterday_docs = $this->createRecentDocuments(10, $target_user, $yesterday);
        $seven_days_docs = $this->createRecentDocuments(5, $member_user, $seven_days);
        $thirty_days_docs = $this->createRecentDocuments(5, $member_user, $thirty_days);
      
        $today_docs->each(function ($document) use ($collection_level_one) {
            $collection_level_one->documents()->save($document);
        });

        $yesterday_docs->each(function ($document) use ($collection_level_two) {
            $collection_level_two->documents()->save($document);
        });

        $seven_days_docs->each(function ($document) use ($collection_level_three) {
            $collection_level_three->documents()->save($document);
        });

        $thirty_days_docs->each(function ($document) use ($collection_level_three, $collection_level_one) {
            $collection_level_three->documents()->save($document);
            $collection_level_one->documents()->save($document);
        });

        return [
            $today_docs,
            $yesterday_docs,
            $seven_days_docs,
            $thirty_days_docs,
        ];
    }
}
