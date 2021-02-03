<?php

use KBox\User;
use KBox\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker, $arguments = []) {
    return [
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'name' => $faker->sentence,
        'color' => 'f1c40f',
        'type' => Group::TYPE_PERSONAL,
        'is_private' => true
    ];
});

$factory->state(Group::class, 'project', function (Faker $faker) { 
    return [
        'type' => Group::TYPE_PROJECT,
        'is_private' => false
    ];
});
