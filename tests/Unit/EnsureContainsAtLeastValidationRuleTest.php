<?php

namespace Tests\Unit;

use Tests\TestCase;
use KBox\Rules\EnsureContainsAtLeast;

class EnsureContainsAtLeastValidationRuleTest extends TestCase
{
    public function values_that_will_cause_the_validator_to_fail()
    {
        return [
            [['pear', 'kiwi'], ['pencil', 'kiwi']],
            [['pear'], []],
            [['pear'], null],
            [['pear'], ''],
            [['pear'], 'pencil'],
        ];
    }
    
    public function values_that_will_cause_the_validator_to_pass()
    {
        return [
            [['pear', 'kiwi'], ['pencil', 'pear', 'kiwi']],
            [['pear'], ['pencil', 'pear', 'kiwi']],
            [['pear', 'pencil', 'kiwi'], ['pencil', 'pear', 'kiwi']],
            [['pear'], ['pear']],
            [['pear'], 'pear'],
        ];
    }

    /**
     * @dataProvider values_that_will_cause_the_validator_to_pass
     */
    public function test_rule_passes_if_all_required_values_are_present($required, $input)
    {
        $rule = new EnsureContainsAtLeast($required);

        $this->assertTrue($rule->passes('', $input));
    }
    
    /**
     * @dataProvider values_that_will_cause_the_validator_to_fail
     */
    public function test_rule_fail_if_some_required_values_are_missing($required, $input)
    {
        $rule = new EnsureContainsAtLeast($required);

        $this->assertFalse($rule->passes('', $input));
    }

    public function test_custom_message_is_returned_if_set()
    {
        $rule = new EnsureContainsAtLeast(['pear', 'kiwi']);

        $rule->setCustomMessage(trans('validation.custom.capabilities.ensure_contains_at_least'));

        $this->assertEquals(trans('validation.custom.capabilities.ensure_contains_at_least'), $rule->message());
    }
}
