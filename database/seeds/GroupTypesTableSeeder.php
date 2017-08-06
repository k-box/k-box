<?php

use KlinkDMS\GroupType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class GroupTypesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('group_types')->delete();

        GroupType::create([ 'type' => GroupType::GENERIC ]);

        GroupType::create([ 'type' => GroupType::FOLDER ]);
    }
}
