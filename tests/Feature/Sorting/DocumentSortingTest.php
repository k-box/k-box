<?php

namespace Tests\Feature\Sorting;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use KBox\Capability;
use KBox\DocumentDescriptor;
use KBox\Starred;
use KBox\User;
use Tests\TestCase;

class DocumentSortingTest extends TestCase
{
    use DatabaseTransactions;

    public function sorting_direction()
    {
        return [
            ['a'],
            ['d'],
        ];
    }
    
    /**
     * @dataProvider sorting_direction
     */
    public function test_my_uploads_can_be_sorted_by_last_update($direction)
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $created_at = Carbon::now();

        $expected_documents = [
            $this->createRecentDocument($user, $created_at),
            $this->createRecentDocument($user, $created_at->copy()->subMinutes(10)),
            $this->createRecentDocument($user, $created_at->copy()->subHours(2)),
        ];

        $url = route('documents.index', ['o' => $direction, 'sc' => 'update_date']);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.documents');
        $response->assertViewHas('sorting');

        $sort = $response->viewData('sorting');

        $this->assertEquals('updated_at', $sort->column);
        $this->assertEquals($direction === 'a' ? 'ASC' : 'DESC', $sort->order);
        $this->assertEquals('date', $sort->type);
        $this->assertEquals('update_date', $sort->field);
        $this->assertEquals($direction, $sort->direction);

        $documents = $response->viewData('documents')->values();

        $this->assertEquals(3, $documents->count());

        if ($direction === 'a') {
            $this->assertEquals([
                $expected_documents[2]->id,
                $expected_documents[1]->id,
                $expected_documents[0]->id,
            ], $documents->pluck('id')->toArray());
        } elseif ($direction === 'd') {
            $this->assertEquals([
                $expected_documents[0]->id,
                $expected_documents[1]->id,
                $expected_documents[2]->id,
            ], $documents->pluck('id')->toArray());
        } else {
            $this->fail('Document sorting is not respecting ASC or DESC');
        }
    }

    protected function createRecentDocument(User $user, Carbon $date = null, $documentParams = [])
    {
        return factory(DocumentDescriptor::class)->create(array_merge([
            'owner_id' => $user->id,
            'created_at' => $date ?? Carbon::now(),
            'updated_at' => $date ?? Carbon::now(),
        ], $documentParams));
    }

    public function test_starred_can_be_sorted_by_last_update()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $created_at = Carbon::now();

        $expected_documents = [
            $this->createRecentDocument($user, null, ['updated_at' => $created_at]),
            $this->createRecentDocument($user, null, ['updated_at' => $created_at->copy()->subMinutes(10)]),
            $this->createRecentDocument($user, null, ['updated_at' => $created_at->copy()->subHours(2)]),
        ];

        foreach ($expected_documents as $doc) {
            factory(Starred::class)->create([
                'user_id' => $user->getKey(),
                'document_id' => $doc->getKey(),
            ]);
        }

        $url = route('documents.starred.index', ['o' => 'd', 'sc' => 'update_date']);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.starred');
        $response->assertViewHas('sorting');

        $sort = $response->viewData('sorting');

        $this->assertEquals('document_descriptors.updated_at', $sort->column);
        $this->assertEquals('DESC', $sort->order);
        $this->assertEquals('date', $sort->type);
        $this->assertEquals('update_date', $sort->field);
        $this->assertEquals('d', $sort->direction);

        $documents = $response->viewData('starred')->values();

        $this->assertEquals(3, $documents->count());

        $this->assertEquals([
            $expected_documents[0]->id,
            $expected_documents[1]->id,
            $expected_documents[2]->id,
        ], $documents->pluck('document.id')->toArray());
    }

    public function test_starred_can_be_sorted_by_title()
    {
        $this->withKlinkAdapterFake();

        $user = tap(factory(User::class)->create())->addCapabilities(Capability::$PARTNER);

        $expected_documents = [
            $this->createRecentDocument($user, null, ['title' => 'a']),
            $this->createRecentDocument($user, null, ['title' => 'z']),
            $this->createRecentDocument($user, null, ['title' => 'f']),
        ];

        foreach ($expected_documents as $doc) {
            factory(Starred::class)->create([
                'user_id' => $user->getKey(),
                'document_id' => $doc->getKey(),
            ]);
        }

        $url = route('documents.starred.index', ['o' => 'a', 'sc' => 'name']);

        $response = $this->actingAs($user)->get($url);

        $response->assertStatus(200);
        $response->assertViewIs('documents.starred');
        $response->assertViewHas('sorting');

        $sort = $response->viewData('sorting');

        $this->assertEquals('document_descriptors.title', $sort->column);
        $this->assertEquals('ASC', $sort->order);
        $this->assertEquals('string', $sort->type);
        $this->assertEquals('name', $sort->field);
        $this->assertEquals('a', $sort->direction);

        $documents = $response->viewData('starred')->values();

        $this->assertEquals(3, $documents->count());

        $this->assertEquals([
            $expected_documents[0]->id,
            $expected_documents[2]->id,
            $expected_documents[1]->id,
        ], $documents->pluck('document.id')->toArray());
    }
}
