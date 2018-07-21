<?php

namespace Tests;

use PHPUnit\Framework\Assert;
use Tests\Concerns\ClearDatabase;
use Klink\DmsAdapter\Traits\MockKlinkAdapter;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MockKlinkAdapter;

    protected function setUp()
    {
        parent::setUp();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });
        
        TestResponse::macro('assertInstanceOf', function ($class) {
            Assert::assertInstanceOf($class, $this->baseResponse);
        });
    }

    protected function setUpTraits()
    {
        parent::setUpTraits();

        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[ClearDatabase::class])) {
            $this->clearDatabase();
        }

        return $uses;
    }

    /**
     * Invokes a private method on an object
     *
     * @param object $object the object to invoke the method on
     * @param string $methodName the name of the method to invoke
     * @param array $parameters the parameters to pass to the method
     * @return mixed the return value of the invoked method
     */
    public function invokePrivateMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    protected function disableExceptionHandling()
    {
        $this->withoutExceptionHandling();
    }
}
