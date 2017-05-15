<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\Project;
use KlinkDMS\Group;
use Illuminate\Support\Facades\Artisan;
use Klink\DmsAdapter\Traits\MockKlinkAdapter;
use Log;

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase {

    use MockKlinkAdapter;

	protected $artisan = null;
    
    protected $baseUrl = 'http://localhost/';
	
	public function setUp()
	{

		parent::setUp();

        $this->resetEvents();

        ini_set('memory_limit', '-1'); // big file, heavy strings, 128M of RAM are not enough
        ini_set('error_reporting', E_ALL); // or error_reporting(E_ALL);
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');

        Log::info('Starting test ' . get_class($this) . ' - ' . $this->getName());
	}

	/**
	 * Creates the application.
	 *
	 * @return \Illuminate\Foundation\Application
	 */
	public function createApplication()
	{
		$app = require __DIR__.'/../bootstrap/app.php';

		$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

		return $app;
	}
    
    
    protected function seedDatabase(){
        $artisan = app()->make('Illuminate\Contracts\Console\Kernel');
// 
// 		// $artisan->call('migrate',array('-n'=>true));
// 
		$artisan->call('db:seed',array('-n'=> true));
    }
    
	
	
	protected function createAdminUser($params = []){
        
        if( Capability::all()->isEmpty() ){
            $this->seedDatabase();
        }
        
        $admin_user = factory(\KlinkDMS\User::class)->create($params);
		
		$admin_user->addCapabilities( Capability::$ADMIN );
		
		return $admin_user;
	}
	
	protected function createUser($capabilities, $user_params = []){
        
        if( Capability::all()->isEmpty() ){
            $this->seedDatabase();
        }
		
		$user = factory(\KlinkDMS\User::class)->create( $user_params );
		
		$user->addCapabilities( $capabilities );
		
		return $user;
	}


    protected function createUsers($capabilities, $count, $user_params = []){
        
        if( Capability::all()->isEmpty() ){
            $this->seedDatabase();
        }
		
		$users = factory(\KlinkDMS\User::class, $count)->create( $user_params );

        $users->each(function($el) use($capabilities){
            $el->addCapabilities( $capabilities );
        });

		return $users;
	}


    protected function createDocument(User $user, $visibility = 'private'){

        $template = base_path('tests/data/example.pdf');
        $destination = storage_path('documents/example-document.pdf');

        copy($template, $destination);

        $file = factory('KlinkDMS\File')->create([
            'user_id' => $user->id,
            'original_uri' => '',
            'path' => $destination,
            'hash' => hash_file('sha512', $destination)
        ]);
        
        $doc = factory('KlinkDMS\DocumentDescriptor')->create([
            'institution_id' => $user->institution_id,
            'owner_id' => $user->id,
            'file_id' => $file->id,
            'hash' => $file->hash,
            'language' => 'en',
            'is_public' => $visibility === 'private' ? false : true
        ]);

        return $doc;

    }

    protected function createCollection(User $user, $is_personal = true, $childs = 0){

        $service = app('Klink\DmsDocuments\DocumentsService');

        $faker = app('Faker\Generator');

        $group = $service->createGroup($user, $faker->name . $user->id, null, null, $is_personal);

        if($childs > 0){

            for ($i=0; $i < $childs; $i++) { 
                $service->createGroup($user, 'Child ' . $user->id . '-' . $group->id . '-' . $i, null, $group, $is_personal);
            }

        }

        return $group;

    }

    protected function createProject($params = []){
        
        return factory('KlinkDMS\Project')->create($params);

    }

    /**
     * Create a project collection under a project collection or a project
     * @param Project|Group $parent
     */
    protected function createProjectCollection(User $user, $parent){

        $group = is_a($parent, 'KlinkDMS\Project') ? $parent->collection : $parent;

        $service = app('Klink\DmsDocuments\DocumentsService');

        $faker = app('Faker\Generator');

        $project_group = $service->createGroup($user, $faker->name . $user->id, null, $group, false);

        return $project_group;

    }
    
    
    public function assertViewName($expected){
        
        try{
        
            if( isset( $this->response ) && !is_string($this->response->original) && !empty( $this->response->original->name() ) ){
                
                $this->assertEquals($expected, $this->response->original->name(), 'View: ' . $this->response->original->name() );
                
                return;
            }
            
            $this->fail('Response does not have a view');

        }catch(\Exception $e){
            $this->fail('Exception while checking view name assertion. ' . $e->getMessage());
        }
        
        
    }




    public function resetEvents()
    {
        $models = $this->getModels();

        foreach ($models as $model)
        {
            call_user_func([$model, 'flushEventListeners']);

            call_user_func([$model, 'boot']);
        }
    }
    
    protected function getModels()
    {
        // Replace with your models directory if you've moved it.
        $files = \File::files(base_path() . '/app/models');
        
        $models = [];

        foreach ($files as $file)
        {
            $models[] = pathinfo($file, PATHINFO_FILENAME);
        }

        return $models;
    }
    
    
    
    function runArtisanCommand($command, $arguments = [])
    {
        $command->setLaravel(app());
        
        $output = new Symfony\Component\Console\Output\BufferedOutput;
        
        $this->runCommand($command, $arguments, $output);
        
        return $output->fetch();
    }
    
    
    public function invokePrivateMethod(&$object, $methodName, array $parameters = array())
	{
	    $reflection = new ReflectionClass(get_class($object));
	    $method = $reflection->getMethod($methodName);
	    $method->setAccessible(true);

	    return $method->invokeArgs($object, $parameters);
	}    
}
