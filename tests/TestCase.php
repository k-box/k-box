<?php

namespace Tests;

use Exception;
use KlinkDMS\Exceptions\Handler;
use Klink\DmsAdapter\Traits\MockKlinkAdapter;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MockKlinkAdapter;

    /**
     * Set the previous visited URL.
     *
     * This is useful for testing redirect to previous location
     * in form validation.
     *
     * @param string $url
     * @return TestCase
     */
    public function from($url)
    {
        session()->setPreviousUrl(url($url));
        return $this;
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
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct()
            {
            }
            public function report(Exception $e)
            {
            }
            public function render($request, Exception $e)
            {
                throw $e;
            }
        });
    }
}
