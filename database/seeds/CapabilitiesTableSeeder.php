<?php

use KBox\Capability;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class CapabilitiesTableSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        DB::table('capabilities')->delete();

        Capability::create([ 'key' => Capability::MANAGE_KBOX ]);

        // Document management capabilities

        Capability::create([ 'key' => Capability::CHANGE_DOCUMENT_VISIBILITY ]);

        Capability::create([ 'key' => Capability::EDIT_DOCUMENT ]);

        Capability::create([ 'key' => Capability::DELETE_DOCUMENT ]);

        Capability::create([ 'key' => Capability::UPLOAD_DOCUMENTS ]);

        Capability::create([ 'key' => Capability::CLEAN_TRASH ]);

        Capability::create([ 'key' => Capability::MANAGE_OWN_GROUPS ]);

        Capability::create([ 'key' => Capability::MANAGE_PROJECT_COLLECTIONS ]);
        
        Capability::create([ 'key' => Capability::CREATE_PROJECTS ]);

        // Search capabilities
        
        Capability::create([ 'key' => Capability::MAKE_SEARCH ]);

        // Share capabilities
        
        Capability::create([ 'key' => Capability::SHARE_WITH_USERS ]);
        
        Capability::create([ 'key' => Capability::RECEIVE_AND_SEE_SHARE ]);
    }
}
