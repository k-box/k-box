<?php

use KBox\User;
use KBox\Invite;
use Faker\Generator as Faker;

$factory->define(Invite::class, function (Faker $faker) {
    return [
        'creator_id' => function () {
            return factory(User::class)->create()->id;
        },
        'uuid' => $faker->uuid,
        'email' => $faker->unique()->safeEmail,
        'token' => $faker->uuid,
        'actionable_id' => null,
        'actionable_type' => null,
        'expire_at' => now()->endOfDay()->addDays(config('invites.expiration'))
    ];
});
