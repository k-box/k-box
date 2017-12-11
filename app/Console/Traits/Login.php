<?php

namespace KBox\Console\Traits;

use KBox\Exceptions\ForbiddenException;
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
     * @var KBox\User
     */
    private $user = null;
    
    
    public function askLogin()
    {
        if (App::environment() === 'testing') {
            return;
        }
        
        $this->info('To run this command you need to specify an administrator account');
        
        $email = $this->ask('User Email?');
        $password = $this->secret($email.' password?');
        
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->user = Auth::getUser();
        } else {
            throw new ForbiddenException('Invalid credentials (or user not found/active) for '.$email);
        }
    }
    
    /** Get the logged in user
     *
     * @return KBox\User
     */
    public function user()
    {
        return $this->user;
    }
}
