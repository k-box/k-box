<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\User;
use KBox\Invite;

class InviteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invite::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'creator_id' => User::factory(),
            'uuid' => $this->faker->uuid,
            'email' => $this->faker->unique()->safeEmail,
            'token' => $this->faker->uuid,
            'actionable_id' => null,
            'actionable_type' => null,
            'expire_at' => now()->endOfDay()->addDays(config('invites.expiration'))
        ];
    }
}
