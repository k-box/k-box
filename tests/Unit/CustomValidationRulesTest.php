<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class CustomValidationRulesTest extends TestCase
{
    public function values_that_will_cause_the_validator_to_fail()
    {
        
        /*
         * Empty or falsy values, like array(), will not be validated as they are
         * considered not added to the array of inputs. If you specify required an empty
         * array will fail because required requires the value to be !empty
         */
        
        return [
            [['body' => ['key' => 'value']]],
            [['body' => ['hi', 'dude']]],
            [['body' => [1, 2]]],
            [['body' => []]],
        ];
    }
    
    public function values_that_will_cause_the_validator_to_not_fail()
    {
        return [
            [['body' => 'array']],
            [['body' => '']],
            [['body' => false]],
            [['body' => true]],
            [['body' => 0]],
            [['body' => 1]],
        ];
    }
    
    // Test not_array rule
    
    /**
     * Test the not_array custom validation extension
     *
     * @dataProvider values_that_will_cause_the_validator_to_fail
     * @return void
     */
    public function testNotArrayValidationRule($params)
    {
        $validator = Validator::make($params, [
            'body' => 'nullable|not_array'
        ]);
        
        $this->assertTrue($validator->fails(), 'I was expecting a validation failure for '.var_export($params, true));

        // check if the custom error message is reported
        $err = $validator->errors()->all();
        $this->assertEquals(trans('validation.not_array', ['attribute' => 'body']), $err[0]);
    }
    
    /**
     * Test the not_array custom validation extension
     *
     * @dataProvider values_that_will_cause_the_validator_to_not_fail
     * @return void
     */
    public function testNotArrayValidationRuleNotFail($params)
    {
        $validator = Validator::make($params, [
            'body' => 'not_array'
        ]);
        
        $this->assertFalse($validator->fails(), 'I was expecting a smooth clean validation for '.var_export($params, true));
    }
    
    public function testNotArrayDefaultLanguageLocalization()
    {
        $this->assertNotEquals('validation.not_array', trans('validation.not_array', ['attribute' => 'body']));
    }
}
