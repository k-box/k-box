<?php

use KlinkDMS\User;
use KlinkDMS\Capability;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class UserTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('users')->delete();

        $the_user = User::create([
            'name' => 'admin',
            'email' =>'admin@klink.local',
            'password' => Hash::make('dms.admin')
        ]);

        $the_user->addCapabilities(Capability::$ADMIN);
    }
}
