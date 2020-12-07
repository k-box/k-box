<?php

namespace Tests;

use Illuminate\Testing\Assert as PHPUnit;
use Tests\Concerns\ClearDatabase;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Klink\DmsAdapter\Traits\MockKlinkAdapter;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, MockKlinkAdapter;

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });
        
        TestResponse::macro('getData', function ($key) {
            return $this->original->getData()[$key];
        });
        
        TestResponse::macro('assertInstanceOf', function ($class) {
            PHPUnit::assertInstanceOf($class, $this->baseResponse);
        });
        
        TestResponse::macro('assertErrorView', function ($status_code) {
            $this->ensureResponseHasView();
            $this->assertViewIs("errors.$status_code");
        });
        
        TestResponse::macro('isView', function () {
            return isset($this->original) && $this->original instanceof View;
        });

        TestResponse::macro('assertViewHasModel', function ($key, $expected) {
            $this->assertViewHas($key);
            $found = $this->viewData($key);

            PHPUnit::assertTrue($expected->is($found));
        });

        TestResponse::macro('assertViewHasModels', function ($key, $expected) {
            $this->assertViewHas($key);
            $found = $this->viewData($key);

            $expected = collect(Arr::wrap($expected));

            PHPUnit::assertInstanceOf(EloquentCollection::class, $found);

            $foundIds = $found->map(function ($i) {
                return $i->getKey();
            });

            $expectedIds = $expected->map(function ($i) {
                return $i->getKey();
            });

            PHPUnit::assertEquals($expectedIds->toArray(), $foundIds->toArray());
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

    /**
     * @deprecated Remove this when upgrading to Laravel 8
     */
    protected function disableExceptionHandling()
    {
        $this->withoutExceptionHandling();
    }

    /**
     * Create a new TestView from the given view.
     *
     * @param  string  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return \Illuminate\Testing\TestView
     * @deprecated Remove this when upgrading to Laravel 8
     */
    protected function render(string $view, array $data = [])
    {
        return new TestView(view($view, $data));
    }

    /**
     * Render the contents of the given Blade template string.
     *
     * @param  string  $template
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return \Illuminate\Testing\TestView
     * @deprecated Remove this when upgrading to Laravel 8
     */
    protected function blade(string $template, array $data = [])
    {
        $tempDirectory = sys_get_temp_dir();

        if (! in_array($tempDirectory, ViewFacade::getFinder()->getPaths())) {
            ViewFacade::addLocation(sys_get_temp_dir());
        }

        $tempFile = $tempDirectory.'/'.Str::random(20).'.blade.php';

        file_put_contents($tempFile, $template);

        $testView = new TestView(view(Str::before(basename($tempFile), '.blade.php'), $data));

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        return $testView;
    }

    /**
     * Render the given view component.
     *
     * @param  string  $componentClass
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @return \Illuminate\Testing\TestView
     * @deprecated Remove this when upgrading to Laravel 8
     */
    protected function component(string $componentClass, array $data = [])
    {
        $component = $this->app->make($componentClass, $data);

        $view = $component->resolveView();

        return $view instanceof View
                ? new TestView($view->with($component->data()))
                : new TestView(view($view, $component->data()));
    }
}
