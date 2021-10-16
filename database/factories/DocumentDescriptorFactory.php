<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\DocumentDescriptor;
use KBox\File;
use KBox\User;

class DocumentDescriptorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DocumentDescriptor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $hash = hash_file('sha512', base_path('tests/data/example.txt'));
        
        return [
            'owner_id' => User::factory()->partner(),
            'local_document_id' => substr($hash, 0, 6),
            'title' => $this->faker->sentence,
            'hash' => $hash,
            'document_uri' => $this->faker->url,
            'thumbnail_uri' => $this->faker->imageUrl,
            'mime_type' => 'text/plain',
            'visibility' => 'private',
            'document_type' => 'document',
            'user_owner' => 'some user <usr@user.com>',
            'user_uploader' => 'some user <usr@user.com>',
            'abstract' => $this->faker->paragraph,
            'language' => $this->faker->languageCode,
            'file_id' => function (array $attributes) {
                return File::factory([
                    'user_id' => User::find($attributes['owner_id'])->id,
                    'original_uri' => '',
                    'upload_completed_at' => Carbon::now()
                ]);
            },
            'status' => DocumentDescriptor::STATUS_COMPLETED,
            'copyright_usage' => 'PD',
            'copyright_owner' => collect([
                'name' => 'the owner name',
                'website' => 'https://k-link.technology'
            ]),
        ];
    }
}
