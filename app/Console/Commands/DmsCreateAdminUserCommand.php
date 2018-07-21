<?php

namespace KBox\Console\Commands;

use Hash;
use Validator;
use Password;
use KBox\User;
use KBox\Capability;
use Illuminate\Console\Command;

/**
 * Creates admin user accounts
 */
final class DmsCreateAdminUserCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-admin {email}
                            {-p|--password= : The user password.}
                            {--show : Show the generated password instead of generating a password reset link.}
                            {-n|--name= : The user name to use. Default the email user.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will create the Administrator of the K-Box.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->option('name', null);
        $password = $this->option('password', null);
        $passwordWasGenerated = false;

        if ($this->validateEmail($email)) {
            $this->error("The specified email $email is not valid.");
            return 3;
        }
        
        if ($this->exists($email)) {
            $this->error("The user \"$email\" already exists.");
            return 2;
        }
            
        if (empty($password) && $this->input->isInteractive()) {
            $password = $this->secret("Please specify an 8 character password for the administrator");
        }

        if (empty($password)) {
            $password = User::generatePassword();
            $passwordWasGenerated = true;
        }
        
        $user = $this->createUser($email, $password, $name);

        $this->line('');
        $this->line("The K-Box Administrator, <comment>$email</comment>, has been created.");
            
        $this->line(
            sprintf(
                $this->option('show', false) && $passwordWasGenerated ? 'A password has been generated for you: <info>%1$s</info> '.PHP_EOL.'Want to change, use this link %2$s' : ($passwordWasGenerated ? 'Set a password for the account using <info>%2$s</info>' : 'Login on %3$s using your chosen password'),
                $password,
                route('password.reset', ['email' => $email, 'token' => Password::createToken($user)]),
                url('/')
            )
        );
        
        $this->line('');

        return 0;
    }

    protected function validateEmail($email)
    {
        $validator = Validator::make(
            ['name' => $email],
            ['name' => 'required|email']
        );

        return $validator->fails();
    }

    protected function exists($email)
    {
        return ! is_null(User::findByEmail($email));
    }

    private function getUsernameFrom($email)
    {
        $et_offset = strpos($email, '@');
        return $et_offset !== false ? substr($email, 0, $et_offset) : $email;
    }

    protected function createUser($email, $password, $name = null)
    {
        $user = User::create([
            'name' => ! empty($name) ? $name : $this->getUsernameFrom($email),
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $user->addCapabilities(Capability::$ADMIN);

        return $user;
    }
}
