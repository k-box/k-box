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

$factory->define(KlinkDMS\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10))
    ];
});

$factory->defineAs(KlinkDMS\User::class, 'admin', function (Faker\Generator $faker) {
    return [
        'name' => 'admin',
        'email' => 'admin@klink.local',
        'password' => bcrypt(str_random(10))
    ];
});


$factory->define(KlinkDMS\File::class, function (Faker\Generator $faker) {
    
    return [
        'name' => $faker->sentence,
        'hash' => $faker->sha256,
        'path' => base_path('tests/data/example.pdf'),
        'mime_type' => 'application/pdf',
        'user_id' => factory(KlinkDMS\User::class)->create()->id, 
        'size' => $faker->randomNumber(2),
    ]; 
});

$factory->define(KlinkDMS\Institution::class, function (Faker\Generator $faker) {
    
    return [
        'klink_id' => str_random(4),
        'email' => $faker->email,
        'type' => $faker->url,
        'url' => 'Organization',
        'thumbnail_uri' => $faker->imageUrl,
        'phone' => $faker->sentence,
        'address_street' => $faker->sentence,
        'address_country' => $faker->sentence,
        'address_locality' => $faker->sentence,
        'address_zip' => $faker->sentence,
        'name' => $faker->sentence
    ]; 
});

$factory->define(KlinkDMS\DocumentDescriptor::class, function (Faker\Generator $faker) {
    
    $hash = $faker->sha256;
    
    $user = factory(KlinkDMS\User::class)->create();
    
    $file = factory(KlinkDMS\File::class)->create([
        'user_id' => $user->id,
        'original_uri' => ''
    ]);
    
    $institution_count = \KlinkDMS\Institution::count();
    
    if($institution_count == 0){
        $institution = factory(KlinkDMS\Institution::class)->create()->id;
    }
    else {
        $institution = \KlinkDMS\Institution::all()->random()->id;
    }
    
    return [
        'institution_id' => $institution,
        'local_document_id' => substr($hash, 0, 6),
        'title' => $faker->sentence,
        'hash' => $hash,
        'document_uri' => $faker->url,
        'thumbnail_uri' => $faker->imageUrl,
        'mime_type' => 'application/pdf',
        'visibility' => 'private',
        'document_type' => 'document',
        'user_owner' => '',
        'user_uploader' => '',
        'abstract' => $faker->paragraph,
        'language' => $faker->languageCode,
        'file_id' => $file->id,
        'owner_id' => $user->id,
    ]; 
});

$factory->define(KlinkDMS\Starred::class, function (Faker\Generator $faker) {

    return [
      'user_id' => factory(KlinkDMS\User::class)->create()->id,
      'document_id' => factory(KlinkDMS\DocumentDescriptor::class)->create()->id
    ];
});


$factory->define(KlinkDMS\Import::class, function (Faker\Generator $faker) {
    
    
    return [
        'bytes_expected' => 0,
        'bytes_received' => 0,
        'is_remote' => true,
        'file_id' => 'factory:KlinkDMS\File',
        'status' => KlinkDMS\Import::STATUS_QUEUED,
        'user_id' => 'factory:KlinkDMS\User',
        'parent_id' => null,
        'status_message' => KlinkDMS\Import::MESSAGE_QUEUED
    ]; 
});

$factory->define(KlinkDMS\Project::class, function (Faker\Generator $faker) {
    
    $institution_count = \KlinkDMS\Institution::count();
    
    if($institution_count == 0){
        $institution = factory(KlinkDMS\Institution::class)->create()->id;
    }
    else {
        $institution = \KlinkDMS\Institution::all()->random()->id;
    }
    
    $user = factory(KlinkDMS\User::class)->create([
        'institution_id' => $institution
    ]);
    
    $user->addCapabilities( KlinkDMS\Capability::$PROJECT_MANAGER_NO_CLEAN_TRASH );
    
    $project_title = $faker->sentence; 
        
    $service = app('Klink\DmsDocuments\DocumentsService');        
        
    $collection = $service->createGroup( $user, $project_title, null, null, false );
    
    return [
        'name' => $project_title,
        'user_id' => $user->id,
        'collection_id' => $collection->id,
    ]; 
});


$factory->define(Klink\DmsMicrosites\Microsite::class, function (Faker\Generator $faker) {
    
    $project = factory(KlinkDMS\Project::class)->create();
    
    $project_manager = $project->manager()->first();
    
    return [
        'project_id' => $project->id,
        'title' => $faker->sentence,
        'slug' => $faker->slug,
        'description' => $faker->paragraph,
        'logo' => str_replace('http://', 'https://', $faker->imageUrl),
        'hero_image' => str_replace('http://', 'https://', $faker->imageUrl),
        'default_language' => 'en',
        'user_id' => $project_manager->id,
        'institution_id' => $project_manager->institution_id,
    ]; 
});

// 
// $factory('KlinkDMS\RecentSearch', [
//   'terms' => $faker->word,
//   'times' => $faker->numberBetween(1, 10),
//   'user_id' => 'factory:KlinkDMS\User'
// ]);
// 
// $factory('KlinkDMS\PeopleGroup', [
//   'name' => $faker->word,
//   'is_institution_group' => $faker->randomElement(array(true, false)),
//   'user_id' => 'factory:KlinkDMS\User'
// ]);
// 
// $factory('KlinkDMS\Shared', function($faker) {
// 
//     return [
//       'token' => $faker->md5,
//       'shareable_id' => 'factory:KlinkDMS\DocumentDescriptor',
//       'shareable_type' => 'KlinkDMS\DocumentDescriptor',
//       'sharedwith_id' => 'factory:KlinkDMS\User',
//       'sharedwith_type' => 'KlinkDMS\User'
//     ];
// });