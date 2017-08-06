<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class InstitutionTableSeeder extends Seeder
{
    private $adapter = null;

    public function __construct(\Klink\DmsAdapter\KlinkAdapter $adapterService)
    {
        $this->adapter = $adapterService;
    }
    

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('institutions')->delete();

        $this->adapter->getInstitutions();
    }
}
