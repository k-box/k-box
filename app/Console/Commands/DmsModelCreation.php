<?php

namespace KBox\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class DmsModelCreation extends Command
{
    use DetectsApplicationNamespace;

    /*

    Str::plural Convert a string to its plural form (English only).

    Str::singular Convert a string to its singular form (English only).

    Str::studly: foo_bar => FooBar

    Str::snake: fooBar => foo_bar

    camel_case: foo_bar => fooBar

     */

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'dms:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an Eloquent model from a migration. If feeded with more than one migration generates more than one model.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $starts = $this->files->glob($this->getMigrationPath().'/2015_03_16_115239_create_shared_table.php');

        foreach ($starts as $migration_to_use) {
            $this->comment(PHP_EOL.$migration_to_use.PHP_EOL);

            $d = $this->getMigrationDetails($migration_to_use);

            $namespace = str_replace('\\', '', $this->getAppNamespace());

            $populated_stub = $this->populateStub($d['table'], $namespace, $this->getStub(), $d['timestamps'], $d['softDelete'], $d['foreignKeys'], $d['fields']);

            $file_name = $this->getClassName($d['table']).'.php';
            $model_file = $this->getModelPath().$file_name;

            if (! $this->files->isFile($model_file)) {
                $this->files->put($model_file, $populated_stub);

                $this->info(PHP_EOL.'model file: '.$model_file.PHP_EOL);
            } else {
                $this->error(PHP_EOL.'Skipped model '.$file_name.': already exists'.PHP_EOL);
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::OPTIONAL, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['single', null, InputOption::VALUE_OPTIONAL, 'Generate a model for the given single migration file.', null],
        ];
    }

    /**
     * The path where to save the newly created models
     * @return string the path of the models
     */
    public function getModelPath()
    {
        return $this->laravel['path'].'/';
    }

    /**
     * The path to the Model's stub folder
     * @return string the stub directory
     */
    private function getStubsPath()
    {
        return __DIR__.'/stubs';
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return $this->laravel['path.database'].'/migrations';
    }

    /**
     * Get the Model stub
     * @param  boolean $is_relation true if the model contains relations
     * @return string        the content of the stub
     */
    private function getStub($is_relation = false)
    {
        if (! $is_relation) {
            return $this->files->get($this->getStubsPath().'/blank.stub');
        } else {
            return $this->files->get($this->getStubsPath().'/relation.stub');
        }
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string  $table
     * @return string
     */
    protected function populateStub($table, $namespace, $stub, $timestamps = false, $softDelete = false, $foreignKeys = null, $comments = [])
    {
        $uses = [ 'use Illuminate\Database\Eloquent\Model;' ];

        $attributes = [];

        $relations = [];

        $declarations = [];

        $stub = str_replace('{{class}}', $this->getClassName($table), $stub);

        $stub = str_replace('{{table}}', $table, $stub);

        $stub = str_replace('{{namespace}}', $namespace, $stub);

        if ($softDelete) {
            $declarations[] = 'use SoftDeletes;';

            $attributes[] = 'protected $dates = [\'deleted_at\'];';
        }

        if (! $timestamps) {
            $attributes[] = 'public $timestamps = false;';
        }

        if (! is_null($foreignKeys) && ! empty($foreignKeys)) {
            $clean_stub = $this->getStub(true);

            $populated_relation_stub ='';
        
            foreach ($foreignKeys as $key) {
                $populated_relation_stub = str_replace('{{relation_method_name}}', Str::camel($this->getClassName($key['table'])), $clean_stub);

                $populated_relation_stub = str_replace('{{model}}', $this->getClassName($key['table']), $populated_relation_stub);

                $populated_relation_stub = str_replace('{{foreign}}', $key['foreign_key'], $populated_relation_stub);

                $populated_relation_stub = str_replace('{{reference}}', $key['references'], $populated_relation_stub);

                $relations[] = $populated_relation_stub;
            }
        }

        $comments_text = '';

        if (! empty($comments)) {
            $comments_text = '    /*'.PHP_EOL.'    '.implode(PHP_EOL.'    ', $comments).PHP_EOL.'    */'.PHP_EOL;
        }

        $attributes_text = '';

        if (! empty($attributes)) {
            $attributes_text = '    '.implode(PHP_EOL, $attributes).PHP_EOL;
        }

        $declarations_text = '';

        if (! empty($declarations)) {
            $declarations_text = '    '.implode(PHP_EOL, $declarations).PHP_EOL;
        }

        $stub = str_replace('{{declare}}', $declarations_text, $stub);

        $stub = str_replace('{{attributes}}', $attributes_text, $stub);

        $stub = str_replace('{{relations}}', implode(PHP_EOL, $relations), $stub);

        $stub = str_replace('{{comments}}', $comments_text, $stub);

        $stub = str_replace('{{use}}', implode(PHP_EOL, $uses), $stub);
        
        return $stub;
    }

    protected function getMigrationDetails($migration)
    {
        $content = $this->files->get($migration);
        
        preg_match_all("/Schema::(create|table)\(\'(.*)\'/", $content, $output_array);

        $table_name = $output_array[2][0];

        preg_match_all("/->(timestamps)\(\)/", $content, $timestamps_array);

        preg_match_all("/->(softDeletes)\(\)/", $content, $softdeletes_array);

        preg_match_all("/->(foreign)\(\'(\w*)\'\)->references\(\'(\w*)\'\)->on\(\'(\w*)\'\)/", $content, $foreign_keys_array);

        $foreign_keys_filtered = [];

        if (isset($foreign_keys_array[1]) && ! empty($foreign_keys_array[1]) && count($foreign_keys_array) == 5) {
            array_push($foreign_keys_filtered, [

                'table' => $foreign_keys_array[count($foreign_keys_array)-1][0],

                'foreign_key' => $foreign_keys_array[2][0],

                'references' => $foreign_keys_array[3][0]

            ]);
        }

        $fields = [];

        preg_match_all("/->(\w+)\(\'(\w+)\'\);/", $content, $fields_array);

        if (! empty($fields_array) && (! empty($fields_array[0]))) {
            $fields_array_count = count($fields_array[0]);

            for ($i=0; $i < $fields_array_count; $i++) {
                $var_type = $fields_array[1][$i];
                $var_name = $fields_array[2][$i];

                $fields[] = $var_name.': '.($var_type !='on' ? $var_type : $this->getClassName($var_name));
            }
        }

        $details = [
            'table' => $table_name,
            'timestamps' => isset($timestamps_array[1]) && ! empty($timestamps_array[1]),
            'softDelete' => isset($softdeletes_array[1]) && ! empty($softdeletes_array[1]),
            'foreignKeys' => $foreign_keys_filtered,
            'fields' => $fields
        ];

        return $details;
    }

    /**
     * Get the class name of a table name.
     *
     * @param  string $name
     * @return string
     */
    protected function getClassName($table)
    {
        return Str::singular(Str::studly($table));
    }
}
