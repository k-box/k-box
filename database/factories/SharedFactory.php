<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\DocumentDescriptor;
use KBox\PublicLink;
use KBox\Shared;
use KBox\User;

class SharedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shared::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'token' => $this->faker->md5,
            'shareable_id' => DocumentDescriptor::factory(),
            'shareable_type' => DocumentDescriptor::class,
            'sharedwith_id' => User::factory(),
            'sharedwith_type' => User::class
        ];
    }

    public function publiclink()
    {
        return $this->state(function (array $attributes) {
            return [
                'sharedwith_id' => function(array $attributes){
                    return PublicLink::factory(['user_id' => $attributes['user_id']]);
                },
                'sharedwith_type' => PublicLink::class
            ];
        });
    }
}
