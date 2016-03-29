<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;



class CustomValidationRulesTest extends TestCase
{
    
    use DatabaseTransactions;
    
    
    function values_that_will_cause_the_validator_to_fail(){
        
        /*
         * Empty or falsy values, like array(), will not be validated as they are 
         * considered not added to the array of inputs. If you specify required an empty 
         * array will fail because required requires the value to be !empty
         */ 
        
        return [
            [['body' => array('key' => 'value')]],
            [['body' => array('hi', 'dude')]],
            [['body' => array(1, 2)]],
        ];
        
    }
    
    function values_that_will_cause_the_validator_to_not_fail(){
        
        return [
            [['body' => []]], 
            [['body' => 'array']],
            [['body' => '']],
            [['body' => false]],
            [['body' => true]],
            [['body' => 0]],
            [['body' => 1]],
        ];
        
    }
    
    
    function empty_if_values_for_failure_test(){
        
        /*
         * Empty or falsy values, like array(), will not be validated as they are 
         * considered not added to the array of inputs. If you specify required an empty 
         * array will fail because required requires the value to be !empty
         */ 
        
        return [
            [[
                'field_one' => 'X',
                'field_two' => 'Y',
                'body' => 'value'
            ]],
            [[
                'field_one' => 'K',
                'field_one' => 'J',
                'body' => 'value'
            ]],
            [[
                'field_one' => 'X',
                'body' => array('hi', 'dude')
            ]],
        ];
        
    }
    
    function empty_if_values_for_pass_test(){
        
        /*
         * Empty or falsy values, like array(), will not be validated as they are 
         * considered not added to the array of inputs. If you specify required an empty 
         * array will fail because required requires the value to be !empty
         */ 
        
        return [
            [[
                'field_one' => 'X',
                'field_two' => 'Y',
                'body' => ''
            ]],
            [[
                'field_one' => 'X',
                'body' => array()
            ]],
            [[
                'field_one' => 'X',
                'body' => null
            ]],
        ];
        
    }
    
    
    // Test not_array rule
    
    /**
     * Test the not_array custom validation extension
     *
     * @dataProvider values_that_will_cause_the_validator_to_fail
     * @return void
     */
    public function testNotArrayValidationRule( $params )
    {

        $validator = Validator::make( $params, [
            'body' => 'not_array'
        ]);
        
        $this->assertTrue($validator->fails(), 'I was expecting a validation failure for ' . var_export($params, true));

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
    public function testNotArrayValidationRuleNotFail( $params )
    {

        $validator = Validator::make( $params, [
            'body' => 'not_array'
        ]);
        
        $this->assertFalse($validator->fails(), 'I was expecting a smooth clean validation for ' . var_export($params, true));

    }
    
    public function testNotArrayDefaultLanguageLocalization(){
        
        $this->assertNotEquals('validation.not_array', trans('validation.not_array', ['attribute' => 'body']));
        
    }
    
    
    // Test empty_if rule
    
    /**
     * Test the empty_if custom validation extension
     *
     * @dataProvider empty_if_values_for_failure_test
     * @return void
     */
    public function testEmptyIfValidationRule( $params )
    {

        $validator = Validator::make( $params, [
            'field_one' => 'required',
            'field_two' => 'sometimes|required',
            'body' => 'empty_if:field_one,X,field_one,K,field_two,Y'
        ]);
        
        $this->assertTrue($validator->fails(), 'I was expecting a validation failure for ' . var_export($params, true));

        // check if the custom error message is reported
        $err = $validator->errors()->all();
        $this->assertEquals(trans('validation.empty_if', ['attribute' => 'body']), $err[0]);

    }
    
    /**
     * Test the empty_if custom validation extension
     *
     * @dataProvider empty_if_values_for_pass_test
     * @return void
     */
    public function testEmptyIfValidationRuleNotFail( $params )
    {
        
        $validator = Validator::make( $params, [
            'field_one' => 'required',
            'field_two' => 'sometimes|required',
            'body' => 'empty_if:field_one,X,field_two,Y'
        ]);
        
        $this->assertFalse($validator->fails(), 'I was expecting a smooth clean validation for ' . var_export($params, true));

    }
    
    public function testEmptyIfDefaultLanguageLocalization(){
        
        $this->assertNotEquals('validation.empty_if', trans('validation.empty_if', ['attribute' => 'body']));
        
    }
    
}