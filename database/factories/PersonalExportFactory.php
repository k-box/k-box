<?php

use KBox\User;
use KBox\PersonalExport;
use Faker\Generator as Faker;

$factory->define(PersonalExport::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'name' => $faker->uuid.'.zip',
        'purge_at' => now()->addMinutes(30),
    ];
});
