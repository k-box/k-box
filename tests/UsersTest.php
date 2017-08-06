<?php

use KlinkDMS\User;
use KlinkDMS\Capability;
use KlinkDMS\Shared;

use Tests\BrowserKitTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Tests user handling
*/
class UsersTest extends BrowserKitTestCase
{
    use DatabaseTransactions;
    
    public function capabilities()
    {
        return [
            
            [Capability::$ADMIN],
            [Capability::$PROJECT_MANAGER],
            [Capability::$PARTNER],
        ];
    }
    
    public function options_values_to_test_for_each_profile()
    {
        return [
            
            [Capability::$ADMIN, User::OPTION_LIST_TYPE, ['details','tiles','cards']],
            [Capability::$PROJECT_MANAGER, User::OPTION_LIST_TYPE, ['details','tiles','cards']],
            [Capability::$PARTNER, User::OPTION_LIST_TYPE, ['details','tiles','cards']],
            [Capability::$GUEST, User::OPTION_LIST_TYPE, ['details','tiles','cards']],
            
            [Capability::$ADMIN, User::OPTION_LANGUAGE, ['en', 'ru']],
            [Capability::$PROJECT_MANAGER, User::OPTION_LANGUAGE, ['en', 'ru']],
            [Capability::$PARTNER, User::OPTION_LANGUAGE, ['en', 'ru']],
            [Capability::$GUEST, User::OPTION_LANGUAGE, ['en', 'ru']],
            
            [Capability::$ADMIN, User::OPTION_TERMS_ACCEPTED, ['1', true]],
            [Capability::$PROJECT_MANAGER, User::OPTION_TERMS_ACCEPTED, ['1', true]],
            [Capability::$PARTNER, User::OPTION_TERMS_ACCEPTED, ['1', true]],
            [Capability::$GUEST, User::OPTION_TERMS_ACCEPTED, ['1', true]],

            [Capability::$ADMIN, User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],
            [Capability::$PROJECT_MANAGER, User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],
            [Capability::$PARTNER, User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],
            [Capability::$GUEST, User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],

            [Capability::$ADMIN, User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
            [Capability::$PROJECT_MANAGER, User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
            [Capability::$PARTNER, User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
            [Capability::$GUEST, User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
        ];
    }

//
// 	/**
// 	 * Tests the basic atomic operations around a user creation, capability checking and softdeleting
// 	 *
// 	 * @return void
// 	 */
// 	public function testUserSeeding()
// 	{
// 		$this->createAdminUser();
//
// 		// check users count
// 		$this->assertEquals(1, User::count());
//
// 		// find by ...
//
// 		$find_by_name = User::findByName('Cooter');
//
// 		$this->assertNull($find_by_name);
//
// 		$find_by_name = User::findByName('admin');
//
// 		$this->assertNotNull($find_by_name);
// 		$this->assertEquals('admin', $find_by_name->name);
// 		$this->assertEquals('admin@klink.local', $find_by_name->email);
//
//
// 		$user = User::findByEmail('admin@klink.local');
//
// 		$this->assertNotNull($user);
// 		$this->assertEquals('admin', $user->name);
// 		$this->assertEquals('admin@klink.local', $user->email);
//
// 		// basic user config checks
//
// 		$this->assertEquals('http://localhost/administration', $user->homeRoute());
//
// 		$this->assertTrue($user->isContentManager());
//
// 		$this->assertTrue($user->isDMSAdmin());
//
// 		$this->assertTrue($user->isDMSManager());
//
// 		// test has capability
//
// 		$this->assertTrue($user->can_capability(Capability::UPLOAD_DOCUMENTS));
//
// 		// test has all capabilities
// 		$this->assertTrue($user->can_capability(Capability::$ADMIN));
//
// 		// test remove capability (1 or more)
//
// 		$user->removeCapability(Capability::UPLOAD_DOCUMENTS);
//
// 		$this->assertFalse($user->can_capability(Capability::UPLOAD_DOCUMENTS), 'Removed UPLOAD_DOCUMENTS capability');
// 		$this->assertFalse($user->can_capability(Capability::$ADMIN), 'Test all ADMIN capavbilities after removing UPLOAD_DOCUMENTS');
//
// 		$user->addCapability(Capability::UPLOAD_DOCUMENTS);
//
// 		$this->assertTrue($user->can_capability(Capability::UPLOAD_DOCUMENTS), 'Adding back a capability');
//
//
// 		// test delete (must be a softdeleted record)
//
// 		$user->delete();
//
// 		//assert if is trashed
// 		$this->assertTrue($user->trashed());
//
//
// 		// completed, basic tests around User and Capability models + HasCapability trait
// 	}
//
// 	/**
// 	 * Tests the relations between the User model and the other entities
// 	 */
// 	public function testUserRelations(){
// 		// create a fake user
// 		// add some fake documents and searches and stuff like that
// 		// tests the relationships methods
//
// 		$user = $this->createAdminUser();
//
// 		$starred = Factory::create('KlinkDMS\Starred', ['user_id' => $user->id]);
//
// 		$groups = Factory::create('KlinkDMS\PeopleGroup', ['user_id' => $user->id]);
//
// 		$docs = Factory::create('KlinkDMS\DocumentDescriptor', ['owner_id' => $user->id]);
//
// 		$searches = Factory::create('KlinkDMS\RecentSearch', ['user_id' => $user->id]);
//
// 		$shared = Factory::create('KlinkDMS\Shared', ['user_id' => $user->id]);
//
// 		$shared_groups = Factory::create('KlinkDMS\Shared', ['token' => 'shared_group', 'user_id' => $user->id, 'sharedwith_id' => $groups->id, 'sharedwith_type' =>'KlinkDMS\PeopleGroup']);
//
// 		$user = $user->fresh();
//
// 		$this->assertEquals(1, $user->starred()->count(), 'Starred count');
// 		$this->assertEquals(1, $user->documents()->count(), 'Documents count');
// 		$this->assertEquals(2, $user->shares()->count(), 'Shares count');
// 		$this->assertEquals(1, Shared::sharedByMe($user)->sharedWithGroup($groups)->count(), 'Shares in groups count');
//
//
// 	}

    /**
     * @dataProvider options_values_to_test_for_each_profile
     */
    public function testUserProfileOptions($cap, $option, $values)
    {
        $user = $this->createUser($cap);
        
        \Session::start(); // Start a session for the current test
        
        $this->actingAs($user);
        
        foreach ($values as $value) {
            $this->post(route('profile.update'), [
                $option => $value,
                '_token' => csrf_token()
            ]);
            
            $this->assertResponseStatus(200);
            
            $this->assertEquals($user->getOption($option)->value, $value);
        }
    }
    
    
    /**
     * @dataProvider capabilities
     */
    public function testTermsMessageShow($cap)
    {
        $this->withKlinkAdapterFake();
        
        $user = $this->createUser($cap);
        
        $this->actingAs($user);
        
        $this->visit(route('documents.index'));
        
        // check if showed

        $this->see(trans('notices.terms_of_use', ['policy_link' => route('terms')]));
        
        // save the option
        
        $user->setOption(User::OPTION_TERMS_ACCEPTED, true);
        
        // reload
        
        $this->visit(route('documents.index'));
        
        // check if not showed
        
        $this->dontSee(trans('notices.terms_of_use', ['policy_link' => route('terms')]));
    }

    public function testOptionPersonalInProjectFilters()
    {
        $user = $this->createAdminUser();

        $this->assertFalse($user->optionPersonalInProjectFilters());
    }

    public function testOptionItemsPerPage()
    {
        $user = $this->createAdminUser();

        $this->assertEquals(config('dms.items_per_page'), $user->optionItemsPerPage());

        $user->setOptionItemsPerPage(1);
        $this->assertEquals(1, $user->optionItemsPerPage());
        
        $user->setOptionItemsPerPage(12);
        $this->assertEquals(12, $user->optionItemsPerPage());
        
        $user->setOptionItemsPerPage(24);
        $this->assertEquals(24, $user->optionItemsPerPage());
        
        $user->setOptionItemsPerPage(50);
        $this->assertEquals(50, $user->optionItemsPerPage());

        $user->setOptionItemsPerPage(100);
        $this->assertEquals(100, $user->optionItemsPerPage());

        try {
            $user->setOptionItemsPerPage(0);
        } catch (\InvalidArgumentException $ix) {
            $this->assertEquals(trans('validation.between.numeric', ['min' => 1, 'max' => 100]), $ix->getMessage());
        }
        try {
            $user->setOptionItemsPerPage(-10);
        } catch (\InvalidArgumentException $ix) {
            $this->assertEquals(trans('validation.between.numeric', ['min' => 1, 'max' => 100]), $ix->getMessage());
        }

        try {
            $user->setOptionItemsPerPage(150);
        } catch (\InvalidArgumentException $ix) {
            $this->assertEquals(trans('validation.between.numeric', ['min' => 1, 'max' => 100]), $ix->getMessage());
        }

        try {
            $user->setOptionItemsPerPage('ciao');
        } catch (\InvalidArgumentException $ix) {
            $this->assertEquals(trans('validation.between.numeric', ['min' => 1, 'max' => 100]), $ix->getMessage());
        }
    }
}
