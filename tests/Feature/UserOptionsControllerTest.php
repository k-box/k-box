<?php

namespace Tests\Feature;

use KBox\User;
use Tests\TestCase;
use KBox\Capability;

class UserOptionsControllerTest extends TestCase
{
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
            [[Capability::RECEIVE_AND_SEE_SHARE], User::OPTION_LIST_TYPE, ['details','tiles','cards']],
            
            [Capability::$ADMIN, User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],
            [Capability::$PROJECT_MANAGER, User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],
            [Capability::$PARTNER, User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],
            [[Capability::RECEIVE_AND_SEE_SHARE], User::OPTION_PERSONAL_IN_PROJECT_FILTERS, ['1', true]],

            [Capability::$ADMIN, User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
            [Capability::$PROJECT_MANAGER, User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
            [Capability::$PARTNER, User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
            [[Capability::RECEIVE_AND_SEE_SHARE], User::OPTION_ITEMS_PER_PAGE, ['1', 12, 24, 50, 100]],
        ];
    }

    /**
     * @dataProvider options_values_to_test_for_each_profile
     */
    public function testUserProfileOptions($cap, $option, $values)
    {
        $user = tap(User::factory()->create(), function ($u) use ($cap) {
            $u->addCapabilities($cap);
        });
        
        $this->actingAs($user);
        
        foreach ($values as $value) {
            $response = $this->put(route('profile.options.update'), [
                $option => $value,
                '_token' => csrf_token()
            ]);
            
            $response->assertRedirect(route('profile.index'));
            
            $this->assertEquals($user->getOption($option)->value, $value);
        }
    }

    public function testOptionPersonalInProjectFilters()
    {
        $user = User::factory()->admin()->create();

        $this->assertFalse($user->optionPersonalInProjectFilters());
    }

    public function testOptionItemsPerPage()
    {
        $user = User::factory()->admin()->create();

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
