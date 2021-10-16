<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use KBox\DocumentDescriptor;
use KBox\DuplicateDocument;
use KBox\User;

class DuplicateDocumentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DuplicateDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
    
        $hash = hash_file('sha512', base_path('tests/data/example.txt'));
            
        return [
            'user_id' => User::factory(),
            'duplicate_document_id' => function (array $attributes) use ($hash){
                return DocumentDescriptor::factory([
                    'user_id' => $attributes['user_id'],
                    'hash' => $hash,
                ]);
            },
            'document_id' => function (array $attributes) use ($hash){
                return DocumentDescriptor::factory([
                    'user_id' => $attributes['user_id'],
                    'hash' => $hash,
                ]);
            },
        ];

    }
}
