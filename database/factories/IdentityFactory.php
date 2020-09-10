<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use KBox\User;
use KBox\Identity;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use KBox\Capability;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Identity::class, function (Faker $faker) {
    return [
        'user_id' =>  function () {
            return factory(User::class)->create()->id;
        },
        'provider_id' => $faker->uuid,
        'provider' => $faker->randomElement(['gitlab', 'dropbox']),
        'token' => $faker->sha256,
        'refresh_token' => null,
        'expires_at' => null,
        'registration' => false,
    ];
});

$factory->state(Identity::class, 'registration', function (Faker $faker) {
    
    return [
        'registration' => true,
    ];
});

$factory->afterCreatingState(User::class, 'admin', function ($user, $faker) {
    $user->addCapabilities([Capability::MANAGE_KBOX]);
});