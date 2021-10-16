<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\Identity;
use KBox\User;

class IdentityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Identity::class;

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
            'provider_id' => $this->faker->uuid,
            'provider' => $this->faker->randomElement(['gitlab', 'dropbox']),
            'token' => $this->faker->sha256,
            'refresh_token' => null,
            'expires_at' => null,
            'registration' => false,
        ];
    }


    public function registration()
    {
        return $this->state(function (array $attributes) {
            return [
                'registration' => true,
            ];
        });
    }
    
}
