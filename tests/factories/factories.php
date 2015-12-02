<?php

$factory('KlinkDMS\User', [
  'email' => $faker->email,
  'name' => $faker->username,
  'password' => 'pass'
]);

$factory('KlinkDMS\User', 'admin_user', [
    'name' => 'admin',
    'email' => 'admin@klink.local',
    'password' => 'pass'
]);

$factory('KlinkDMS\File', [
  'name' => $faker->sentence,
  'hash' => $faker->sha256,
  'path' => '/path/to/file.pdf',
  'mime_type' => 'application/pdf',
  'user_id' => 'factory:KlinkDMS\User', 
  'size' => $faker->randomNumber(2), 
]);


$factory('KlinkDMS\DocumentDescriptor', function($faker) {

    $hash = $faker->sha256;

    return [
      'institution_id' => \KlinkDMS\Institution::all()->first()->id,
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
      'file_id' => 'factory:KlinkDMS\File',
      'owner_id' => 'factory:KlinkDMS\User',
    
    ];
} );

$factory('KlinkDMS\Starred', [
  'user_id' => 'factory:KlinkDMS\User',
  'document_id' => 'factory:KlinkDMS\DocumentDescriptor'
]);

$factory('KlinkDMS\RecentSearch', [
  'terms' => $faker->word,
  'times' => $faker->numberBetween(1, 10),
  'user_id' => 'factory:KlinkDMS\User'
]);

$factory('KlinkDMS\PeopleGroup', [
  'name' => $faker->word,
  'is_institution_group' => $faker->randomElement(array(true, false)),
  'user_id' => 'factory:KlinkDMS\User'
]);

$factory('KlinkDMS\Shared', function($faker) {

    return [
      'token' => $faker->md5,
      'shareable_id' => 'factory:KlinkDMS\DocumentDescriptor',
      'shareable_type' => 'KlinkDMS\DocumentDescriptor',
      'sharedwith_id' => 'factory:KlinkDMS\User',
      'sharedwith_type' => 'KlinkDMS\User'
    ];
});