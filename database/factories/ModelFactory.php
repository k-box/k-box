<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Illuminate\Support\Str;

$factory->define(KBox\User::class, function (Faker\Generator $faker) {
    
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(Str::random(10)),
        'institution_id' => null,
        'email_verified_at' => now()
    ];
});

$factory->defineAs(KBox\User::class, 'admin', function (Faker\Generator $faker) {
    
    return [
        'name' => 'admin',
        'email' => 'admin@klink.local',
        'password' => bcrypt(Str::random(10)),
        'institution_id' => null
    ];
});

$factory->define(KBox\File::class, function (Faker\Generator $faker) {
    $hash = $faker->sha256.''.$faker->sha256;

    if (!is_dir(storage_path('documents'))) {
        mkdir(storage_path('documents'));
    }

    copy(base_path('tests/data/example.txt'), storage_path('documents/example.txt'));
    $path = storage_path('documents/example.txt');
    
    return [
        'name' => $faker->sentence,
        'hash' => hash_file('sha512', $path),
        'path' => $path,
        'mime_type' => 'text/plain',
        'user_id' => function () {
            return factory(KBox\User::class)->create()->id;
        },
        'size' => $faker->randomNumber(2),
        'original_uri' => '',
        'upload_completed_at' => \Carbon\Carbon::now()
    ];
});

$factory->define(KBox\Institution::class, function (Faker\Generator $faker) {
    return [
        'klink_id' => Str::random(4),
        'email' => $faker->email,
        'url' => $faker->url,
        'type' => 'Organization',
        'thumbnail_uri' => $faker->imageUrl,
        'phone' => $faker->sentence,
        'address_street' => $faker->sentence,
        'address_country' => $faker->sentence,
        'address_locality' => $faker->sentence,
        'address_zip' => $faker->sentence,
        'name' => $faker->sentence
    ];
});

$factory->define(KBox\DocumentDescriptor::class, function (Faker\Generator $faker, $arguments = []) {
    
    // $hash = $faker->sha256.''.$faker->sha256;
    $hash = hash_file('sha512', base_path('tests/data/example.txt'));
    
    $user = isset($arguments['owner_id']) ? $arguments['owner_id'] : factory(KBox\User::class)->create()->id;
    
    $file = isset($arguments['file_id']) ? $arguments['file_id'] : factory(KBox\File::class)->create([
        'user_id' => $user,
        'original_uri' => '',
        'upload_completed_at' => \Carbon\Carbon::now()
    ])->id;
    
    return [
        'institution_id' => null,
        'local_document_id' => substr($hash, 0, 6),
        'title' => $faker->sentence,
        'hash' => $hash,
        'document_uri' => $faker->url,
        'thumbnail_uri' => $faker->imageUrl,
        'mime_type' => 'text/plain',
        'visibility' => $arguments['visibility'] ?? 'private',
        'document_type' => 'document',
        'user_owner' => 'some user <usr@user.com>',
        'user_uploader' => 'some user <usr@user.com>',
        'abstract' => $faker->paragraph,
        'language' => $faker->languageCode,
        'file_id' => $file,
        'owner_id' => $user,
        'status' => KBox\DocumentDescriptor::STATUS_COMPLETED,
        'copyright_usage' => 'PD',
        'copyright_owner' => collect(['name' => 'the owner name', 'website' => 'https://k-link.technology']),
    ];
});

$factory->define(KBox\Starred::class, function (Faker\Generator $faker) {
    return [
      'user_id' => function () {
          return factory(KBox\User::class)->create()->id;
      },
      'document_id' => function () {
          return factory(KBox\DocumentDescriptor::class)->create()->id;
      }
    ];
});

$factory->define(KBox\Project::class, function (Faker\Generator $faker, $arguments = []) {
    
    $user = isset($arguments['user_id']) ? KBox\User::findOrFail($arguments['user_id']) : factory(KBox\User::class)->create();
    
    if (!isset($arguments['user_id'])) {
        $user->addCapabilities(KBox\Capability::$PROJECT_MANAGER);
    }
    
    $project_title = $faker->sentence;
    
    $collection = KBox\Group::create([
        'user_id' => $user->id,
        'name' => $project_title,
        'color' => 'f1c40f',
        'group_type_id' => KBox\GroupType::getGenericType()->id,
        'is_private' => false,
        'created_at' => isset($arguments['created_at']) ? $arguments['created_at'] : Carbon\Carbon::now(),
    ]);

    $collection->makeRoot(0);
    
    return [
        'name' => $project_title,
        'user_id' => $user->id,
        'collection_id' => $collection->id,
    ];
});

$factory->define(Klink\DmsMicrosites\Microsite::class, function (Faker\Generator $faker) {
    $project = factory(KBox\Project::class)->create();
    
    return [
        'project_id' => function () use($project) {
            return $project->id;
        },
        'title' => $faker->sentence,
        'slug' => $faker->slug,
        'description' => $faker->paragraph,
        'logo' => str_replace('http://', 'https://', $faker->imageUrl),
        'hero_image' => str_replace('http://', 'https://', $faker->imageUrl),
        'default_language' => 'en',
        'user_id' => function () use($project) {
            return $project->manager()->first()->id;
        },
    ];
});

$factory->define(KBox\Shared::class, function (Faker\Generator $faker) {
    return [
      'user_id' => function () {
          return factory(KBox\User::class)->create()->id;
      },
      'token' => $faker->md5,
      'shareable_id' => function () {
          return factory(KBox\DocumentDescriptor::class)->create()->id;
      },
      'shareable_type' => \KBox\DocumentDescriptor::class,
      'sharedwith_id' => function () {
          return factory(KBox\User::class)->create()->id;
      },
      'sharedwith_type' => \KBox\User::class
    ];
});

$factory->define(KBox\PublicLink::class, function (Faker\Generator $faker) {
    return [
      'user_id' => function () {
          return factory(KBox\User::class)->create()->id;
      },
      'slug' => $faker->slug,
    ];
});

$factory->defineAs(KBox\Shared::class, 'publiclink', function (Faker\Generator $faker, $arguments = []) {
    $link = factory(KBox\PublicLink::class)->create(collect($arguments)->only('user_id')->toArray());

    return [
      'user_id' => $link->user_id,
      'token' => $faker->md5,
      'shareable_id' => function () {
          return factory(KBox\DocumentDescriptor::class)->create()->id;
      },
      'shareable_type' => \KBox\DocumentDescriptor::class,
      'sharedwith_id' => $link->id,
      'sharedwith_type' => \KBox\PublicLink::class
    ];
});


$factory->define(KBox\Group::class, function (Faker\Generator $faker, $arguments = []) {
    
    return [
        'user_id' => function () {
            return factory(KBox\User::class)->create()->id;
        },
        'name' => $faker->sentence,
        'color' => 'f1c40f',
        'group_type_id' => KBox\GroupType::getGenericType()->id,
        'is_private' => false
    ];
});

$factory->define(KBox\DuplicateDocument::class, function (Faker\Generator $faker, $arguments = []) {
    
    // $hash = $faker->sha256.''.$faker->sha256;
    $hash = hash_file('sha512', base_path('tests/data/example.txt'));
    
    $user = isset($arguments['user_id']) ? $arguments['user_id'] : factory(KBox\User::class)->create()->id;
    
    $duplicate = isset($arguments['duplicate_document_id']) ? $arguments['duplicate_document_id'] : factory(KBox\DocumentDescriptor::class)->create([
        'owner_id' => $user,
        'hash' => $hash,
    ])->id;
    
    $existing = isset($arguments['document_id']) ? $arguments['document_id'] : factory(KBox\DocumentDescriptor::class)->create([
        'owner_id' => $user,
        'hash' => $hash,
    ])->id;
    
    return [
        'duplicate_document_id' => $duplicate,
        'document_id' => $existing,
        'user_id' => $user,
    ];
});
