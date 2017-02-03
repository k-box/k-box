# Unit Test

Unit test is done with PHPUnit and the Laravel testing framework. 

To perform the unit tests you must have done a full `composer install` with also the development dependencies.

## environment: `testing`

testing environment configuration is in the `testing.env` file and in the `phpunit.xml` file.

Prior to the execution of the unit tests you must migrate and seed the database

```bash
$ php artisan migrate --env=testing

$ php artisan db:seed --env=testing
```

After that you can run

```
$ ./vendor/bin/phpunit
```

## Helper methods

To speed up the creation of unit tests, the base `TestCase` classes offers some helper methods.

### Swapping an instance in the service container

For convenience is possible to swap an instance of a service with an alternative implementation.

To do so use the `swap` method, which accepts the class or interface full (or service) name and the 
new instance to be used as the substitute. Use this method before calling other methods that uses 
the class/service you want to swap.

```
$this->swap('Class', new SubstituteClass());
```

### Using a KlinkAdapter mock

If you don't want to require a running K-Core instance for executing your tests, a mock of the 
KlinkAdapter can be instantiated and used when needed. To do so call

```
$mock = $this->withKlinkAdapterMock();
```

at the beginning of your unit test. The method returns the Mockery\MockInterface instance that 
you can use to set expectations on method calls. At the same time the mock instance is added to 
the Laravel service container for binding resolution.
