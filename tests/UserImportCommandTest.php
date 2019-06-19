<?php

use KBox\User;
use KBox\Project;
use KBox\Capability;
use Illuminate\Support\Facades\Artisan;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use Illuminate\Foundation\Application;
use KBox\Console\Commands\DmsUserImportCommand;

/*
 * Test the DmsUserImportCommand
*/
class UserImportCommandTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function user_provider_for_editpage_public_checkbox_test()
    {
        return [
            [Capability::$ADMIN, true],
            [[Capability::MANAGE_KBOX], false],
            [Capability::$PROJECT_MANAGER, true],
            [Capability::$PARTNER, false],
        ];
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Not enough arguments (missing: "file").
     */
    public function testDmsUserImportCommandWithNoFile()
    {
        $command = new DmsUserImportCommand();
        
        $app = new Application();
        $command->setLaravel($app);
        
        $this->runCommand($command, []);
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The file ./data/totally-non-existing-file.csv cannot be found or readed
     */
    public function testDmsUserImportCommandWithNonExistingFile()
    {
        $command = new DmsUserImportCommand();
        
        $app = new Application();
        $command->setLaravel($app);
        
        $this->runCommand($command, ['file' => './data/totally-non-existing-file.csv']);
    }
    
    public function testDmsUserImportCommandWithValidFileAndFiveColumns()
    {
        $user = $this->createAdminUser();
        
        \Mail::shouldReceive('queue')->times(4)->withAnyArgs();
        
        // create a Project called "test"
        $test = factory(\KBox\Project::class)->create(['name' => 'test']);
        // create a Project called "secondary"
        $secondary = factory(\KBox\Project::class)->create(['name' => 'secondary']);
        // create a project called "lead by"
        $lead_by = factory(\KBox\Project::class)->create(['name' => 'lead by']);
        
        $command = new DmsUserImportCommand();
        
        $app = new Application();
        $command->setLaravel($app);
        
        $output = new Symfony\Component\Console\Output\BufferedOutput;
        
        $this->runCommand($command, [
            'file' => __DIR__.'/data/users.csv',
            '--delimiter' => ';',
            '--value-delimiter' => ',',
            ], $output);
        // php artisan users:import --delimiter=; --value-delimiter=, ./tests/data/users.csv
        
        $res = $output->fetch();
        
        $user1 = User::findByEmail('user-1@k-link.technology');
        
        $user3 = User::findByEmail('user-3@k-link.technology');
        $user5 = User::findByEmail('user-5@k-link.technology');
        
        $this->assertNotNull($user1);
        $this->assertNotNull($user3);
        $this->assertNotNull($user5);
        
        $this->assertTrue($user1->can_all_capabilities(Capability::$PARTNER), "User 1 is not partner");
        $this->assertTrue($user3->can_all_capabilities(Capability::$PROJECT_MANAGER_LIMITED), "User 3 is not project manager");
        $this->assertTrue($user5->isDMSAdmin(), "User 5 is not an admin");
        
        $lead_by = Project::findOrFail($lead_by->id); // get a refreshed version of the model, otherwise the change cannot be tested
        
        // check if user3 is the project admin of "lead by"
        $this->assertEquals($user3->id, $lead_by->user_id, 'user3 is not project admin of "lead by"');
        // check if user3 is added to "test" and "secondary"
        $this->assertNotNull($test->users()->where('users.id', $user3->id)->first(), 'user3 is not added to "test"');
        $this->assertNotNull($secondary->users()->where('users.id', $user3->id)->first(), 'user3 is not added to "secondary"');
        // check if user1 is added to "test"
        $this->assertNotNull($test->users()->where('users.id', $user1->id)->first(), 'user1 not added to "test"');
        
        // check if user6,7 are listed with errors
        // user6 -> add_to_project error
        // user6 -> role is partner and has also Project manager field
        $this->assertRegExp('/([\|\s]*)user-6([\s\w\S\W.]*)5    | The selected role is invalid. - The selected manage projects is invalid. - The selected add to projects is invalid\./', $res);
        
        // user7 -> add_to_project error
        $this->assertRegExp('/([\|\s]*)user-7([\s\w\S\W.]*)6    \| The selected add to projects is invalid\./', $res);
    }
    
    public function testDmsUserImportCommandWithValidFileWithThreeColumns()
    {
        $user = $this->createAdminUser();
        
        \Mail::shouldReceive('queue')->times(6)->withAnyArgs();
        
        $command = new DmsUserImportCommand();
        
        $app = new Application();
        $command->setLaravel($app);
        
        $output = new Symfony\Component\Console\Output\BufferedOutput;
        
        $this->runCommand($command, [
            'file' => __DIR__.'/data/users-less-columns.csv',
            '--delimiter' => ';',
            '--value-delimiter' => ',',
            ], $output);
        // php artisan users:import --delimiter=; --value-delimiter=, ./tests/data/users.csv
        
        $res = $output->fetch();

        $users = User::whereIn('email', [
            'user-11@k-link.technology',
            'user-13@k-link.technology',
            'user-14@k-link.technology',
            'user-15@k-link.technology',
            'user-16@k-link.technology',
            'user-17@k-link.technology',
        ])->get();
        
        $this->assertEquals(6, $users->count());
    }
    
    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Wrong column name, expecting manage project or manage or manage-projects or manage-project found k-linker at index 3
     */
    public function testDmsUserImportCommandWithWrongColumnsInFile()
    {
        $user = $this->createAdminUser();
        
        $command = new DmsUserImportCommand();
        
        $app = new Application();
        $command->setLaravel($app);
        
        $output = new Symfony\Component\Console\Output\BufferedOutput;
        
        $this->runCommand($command, [
            'file' => __DIR__.'/data/users-wrong-columns.csv',
            '--delimiter' => ';',
            '--value-delimiter' => ',',
            ], $output);
        
        // $res = $output->fetch();
    }
    
    protected function runCommand($command, $input = [], $output = null)
    {
        if (is_null($output)) {
            $output = new Symfony\Component\Console\Output\NullOutput;
        }
        
        return $command->run(new Symfony\Component\Console\Input\ArrayInput($input), $output);
    }
}
