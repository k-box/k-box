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
