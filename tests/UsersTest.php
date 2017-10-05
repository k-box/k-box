<?php

use KlinkDMS\User;
use KlinkDMS\Capability;

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
