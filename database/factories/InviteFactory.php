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
        'actionable_id' => null,
        'actionable_type' => null,
    ];
});
