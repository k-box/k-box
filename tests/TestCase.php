<?php

use Laracasts\TestDummy\Factory;
use KlinkDMS\User;
use KlinkDMS\Capability;
use Illuminate\Support\Facades\Artisan;

class TestCase extends Illuminate\Foundation\Testing\TestCase {


	protected $artisan = null;
    
    protected $baseUrl = 'http://localhost/';
	
	public function setUp()
	{

		parent::setUp();

		

		// $artisan->call('migrate',array('-n'=>true));

		// $artisan->call('db:seed',array('-n'=>true));
		//dd(env('DB_NAME'));
		
		//let's create some users to simulate different kinds of users
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
    
	
	
	protected function createAdminUser(){
        
        if( Capability::all()->isEmpty() ){
            $this->seedDatabase();
            
            var_dump( 'Database seeding completed' );
        }
        
        $admin_user = factory(\KlinkDMS\User::class)->create();
		
		$admin_user->addCapabilities( Capability::$ADMIN );
		
		return $admin_user;
	}
	
	protected function createUser($capabilities, $user_params = []){
        
        if( Capability::all()->isEmpty() ){
            $this->seedDatabase();
            
            var_dump( 'Database seeding completed' );
        }
		
		$user = factory(\KlinkDMS\User::class)->create( $user_params );
		
		$user->addCapabilities( $capabilities );
		
		return $user;
	}
    
    
    public function assertViewName($expected){
        
        try{
        
            if( isset( $this->response ) && !empty( $this->response->original->name() ) ){
                
                $this->assertEquals($expected, $this->response->original->name() );
                
                return;
            }
            
            $this->fail('Response does not have a view');
        
        
        }catch(\Exception $e){
            $this->fail('Exception while checking view name assertion. ' . $e->getMessage());
        }
        
        
    }

}
