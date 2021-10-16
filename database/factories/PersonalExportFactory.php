<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\PersonalExport;
use KBox\User;

class PersonalExportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PersonalExport::class;

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
            'name' => $this->faker->uuid.'.zip',
            'purge_at' => now()->addMinutes(30),
        ];
    }
}
