# Unit Test

Unit test is done with PHPUnit and the Laravel testing framework. 

To perform the unit tests do a full `composer install` with also the development dependencies.

Now create a `testing.env` file. This file contains the environment configuration under which uni tests are executed. By default Laravel will set the environment to `testing` (i.e. `APP_ENV=testing`)

Prior to the execution of the unit tests you must migrate and seed the database

```bash
$ php artisan config:clear

$ php artisan migrate --env=testing

$ php artisan db:seed --env=testing
```

After that you can run

```
$ ./vendor/bin/phpunit
```

> Please consider that the documentation might be out-dated as Laravel 5.5 deprecated the core support for BrowserKit tests. Currently most of the unit tests in the K-Box code are BrowserKit based. **Newly created tests should follow the Laravel 5.5 (and above) approach.**

## Helper methods

To speed up the creation of unit tests, the base `TestCase` class offers some helper methods.

### Swapping an instance in the service container

For convenience is possible to swap an instance of a service with an alternative implementation.

To do so use the `swap` method, which accepts the class or interface full (or service) name and the 
new instance to be used as the substitute. Use this method before calling other methods that uses 
the class/service you want to swap.

```
$this->swap('Class', new SubstituteClass());
```

## Mocking

Beside the [Mocks](https://laravel.com/docs/5.5/mocking) and testing helpers defined 
by Laravel, the K-Box offers some utilities.

### KlinkAdapter mock

If you don't want to require a running K-Core instance for executing your tests, a mock of the 
KlinkAdapter can be instantiated and used when needed. To do so call

```
$mock = $this->withKlinkAdapterMock();
```

at the beginning of your unit test. The method returns the Mockery\MockInterface instance that 
you can use to set expectations on method calls. At the same time the mock instance is added to 
the Laravel service container for binding resolution.

## DocumentElaboration Fake

As an alternative to mocking, you may use the `DocumentElaboration` facade's `fake` method to 
prevent the elaboration pipeline from executing. You may then assert that a pipeline was 
enqueued for a specific document. When using fakes, assertions are made after the code 
under test is executed.
