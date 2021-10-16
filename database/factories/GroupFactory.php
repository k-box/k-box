<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\User;
use KBox\Group;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory();
            },
            'name' => $this->faker->sentence,
            'color' => 'f1c40f',
            'type' => Group::TYPE_PERSONAL,
            'is_private' => true
        ];
    }


    public function project()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Group::TYPE_PROJECT,
                'is_private' => false,
            ];
        });
    }
    
}
