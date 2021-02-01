<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        /**
         * Seed the capabilities table for default capabilities to be associated to a user
         */
        $this->call('CapabilitiesTableSeeder');
    }
}
