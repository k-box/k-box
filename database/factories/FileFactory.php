<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\File;
use \Carbon\Carbon;
use KBox\User;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (!is_dir(storage_path('documents'))) {
            mkdir(storage_path('documents'));
        }

        copy(base_path('tests/data/example.txt'), storage_path('documents/example.txt'));
        $path = storage_path('documents/example.txt');
        
        return [
            'name' => $this->faker->sentence,
            'hash' => hash_file('sha512', $path),
            'path' => $path,
            'mime_type' => 'text/plain',
            'user_id' => function (array $attributes) {
                return User::factory()->partner();
            },
            'size' => $this->faker->randomNumber(2),
            'original_uri' => '',
            'upload_completed_at' => Carbon::now()
        ];
    }
}
