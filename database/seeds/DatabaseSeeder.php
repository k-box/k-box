<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		/**
		 * Seed the group types tabel
		 */
		$this->call('GroupTypesTableSeeder');

		/**
		 * Seed the capabilities table for default capabilities to be associated to a user
		 */
		$this->call('CapabilitiesTableSeeder');

		/**
		 * Seed the institutions table using the K-Link Core obtained institutions list
		 */
		$this->call('InstitutionTableSeeder');


		// $this->call('UserTableSeeder');

	}

}
