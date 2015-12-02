<?php

use KlinkDMS\Capability;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CapabilitiesTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		DB::table('capabilities')->delete();


		// DMS related capabilities

		Capability::create(array( 'key' => Capability::MANAGE_DMS, 'description' => 'The user can access to the DMS administration panel.' ));

		Capability::create(array( 'key' => Capability::MANAGE_USERS, 'description' => 'The user can manage DMS users.' ));

		Capability::create(array( 'key' => Capability::MANAGE_LOG, 'description' => 'The user can see the DMS logs.' ));

		Capability::create(array( 'key' => Capability::MANAGE_BACKUP, 'description' => 'The user can perform DMS backups and restore.' ));



		// Document management capabilities

		Capability::create(array( 'key' => Capability::CHANGE_DOCUMENT_VISIBILITY, 'description' => 'The user can change the visibility of the documents.' ));

		Capability::create(array( 'key' => Capability::EDIT_DOCUMENT, 'description' => 'The user can edit documents.' ));

		Capability::create(array( 'key' => Capability::DELETE_DOCUMENT, 'description' => 'The user can delete documents.' ));

		Capability::create(array( 'key' => Capability::IMPORT_DOCUMENTS, 'description' => 'The user can bulk import documents from folders or external URL.' ));

		Capability::create(array( 'key' => Capability::UPLOAD_DOCUMENTS, 'description' => 'The user can upload documents.' ));

		Capability::create(array( 'key' => Capability::CLEAN_TRASH, 'description' => 'The user can clean the trash.' ));

		Capability::create(array( 'key' => Capability::MANAGE_OWN_GROUPS, 'description' => 'The user can manage own groups.' ));

		Capability::create(array( 'key' => Capability::MANAGE_PROJECT_COLLECTIONS, 'description' => 'The user can manage project collections.' ));


		// Search capabilities
		
		Capability::create(array( 'key' => Capability::MAKE_SEARCH, 'description' => 'The user can perform search query on the institution documents.' ));


		// Share capabilities
		
		Capability::create(array( 'key' => Capability::SHARE_WITH_PERSONAL, 'description' => 'The user create and manage shares.' ));
		
		Capability::create(array( 'key' => Capability::SHARE_WITH_PRIVATE, 'description' => 'The user create and manage shares also with institution defined people groups.' ));
		
		Capability::create(array( 'key' => Capability::RECEIVE_AND_SEE_SHARE, 'description' => 'The user can see documents that has been shared with him.' ));
		
		
		// People groups capabilities
		
		Capability::create(array( 'key' => Capability::MANAGE_PEOPLE_GROUPS, 'description' => 'Create/edit/remove groups of people at the institution level.' ));
		
		Capability::create(array( 'key' => Capability::MANAGE_PERSONAL_PEOPLE_GROUPS, 'description' => 'Create/edit/remove groups of people at the user\'s personal level.' ));

		
	}

}
