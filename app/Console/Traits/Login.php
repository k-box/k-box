<?php namespace KlinkDMS\Console\Traits;

use KlinkDMS\Exceptions\ForbiddenException;
use Auth;
use App;

/**
 * Asks For Login in a Console Command.
 *
 * Must be used inside a class that extends Illuminate\Console\Command
 */
trait Login
{
    
    /**
     * @var KlinkDMS\User
     */
    private $user = null;
    
    
    function askLogin()
    {
        
        if( App::environment() === 'testing' ){
            return;
        } 
        
        $this->info('To run this command you need to specify an administrator account');
        
        $email = $this->ask('User Email?');
        $password = $this->secret( $email . ' password?');
        
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            
            $this->user = Auth::getUser();
            
        }
        else {
            throw new ForbiddenException('Invalid credentials (or user not found/active) for ' . $email);
        }
        
    }
    
    /** Get the logged in user
     *
     * @return KlinkDMS\User
     */
    function user(){
        return $this->user;
    }
}
