<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use KBox\User;
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

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10),
    ];
});

$factory->state(User::class, 'admin', function (Faker $faker) {
    
    return [
        'name' => 'admin',
        'email' => 'admin@klink.local',
    ];
});

$factory->afterCreatingState(User::class, 'admin', function ($user, $faker) {
    $user->addCapabilities([Capability::MANAGE_KBOX]);
});

$factory->state(User::class, 'partner', function (Faker $faker) {
    return [];
});

$factory->afterCreatingState(User::class, 'partner', function ($user, $faker) {
    $user->addCapabilities(Capability::$PARTNER);
});

$factory->state(User::class, 'project-manager', function (Faker $faker) {
    return [];
});

$factory->afterCreatingState(User::class, 'project-manager', function ($user, $faker) {
    $user->addCapabilities(Capability::$PROJECT_MANAGER);
});