<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\Project;
use KBox\User;
use Carbon\Carbon;
use KBox\Group;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $project_title = $this->faker->sentence;
        
        return [
            'name' => $project_title,
            'user_id' => User::factory()->projectManager(),
            'collection_id' => function (array $attributes) {
                return Group::factory([
                    'user_id' => $attributes['user_id'],
                    'name' => $attributes['name'],
                    'color' => 'f1c40f',
                    'type' => Group::TYPE_PROJECT,
                    'is_private' => false,
                    'created_at' => isset($attributes['created_at']) ? $attributes['created_at'] : Carbon::now(),
                    'parent_id' => null,
                    'position' => 0,
                ]);
            },
        ];
    }
}
