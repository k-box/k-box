<?php

namespace Tests\Unit\Middleware;

use App;
use KBox\User;
use Tests\TestCase;
use Illuminate\Http\Request;
use KBox\Http\Middleware\Locale;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LocaleMiddlewareTest extends TestCase
{
    use DatabaseTransactions;

    public function test_configured_language_is_selected_if_no_preference_is_specified()
    {
        config(['app.locale' => 'de']);

        $request = new Request;

        $middleware = new Locale();

        $next = new class {
            // anonymous invokable class to track a state for the next closure
            public $called = false;

            public function __invoke($args)
            {
                $this->called = true;
                return $args;
            }
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($next->called);
        $this->assertSame($response, $request);
        $this->assertEquals('de', App::getLocale());
    }

    public function test_user_language_is_selected_if_no_preference_is_specified()
    {
        $user = factory('KBox\User')->create();

        $user->setOption(User::OPTION_LANGUAGE, 'fr');

        config(['app.locale' => 'de']);

        $this->be($user);

        $request = new Request;

        $middleware = new Locale();

        $next = new class {
            // anonymous invokable class to track a state for the next closure
            public $called = false;

            public function __invoke($args)
            {
                $this->called = true;
                return $args;
            }
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($next->called);
        $this->assertSame($response, $request);
        $this->assertEquals('fr', App::getLocale());
    }

    public function test_user_language_is_selected_even_if_browser_preference_is_specified()
    {
        config(['app.locale' => 'de']);

        $user = factory('KBox\User')->create();

        $user->setOption(User::OPTION_LANGUAGE, 'fr');

        $this->be($user);

        $request = Request::create('/', 'GET', [], [], [], [
            'HTTP_ACCEPT_LANGUAGE' => 'en-us,en;q=0.5',
        ]);

        $middleware = new Locale();

        $next = new class {
            // anonymous invokable class to track a state for the next closure
            public $called = false;

            public function __invoke($args)
            {
                $this->called = true;
                return $args;
            }
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($next->called);
        $this->assertSame($response, $request);
        $this->assertEquals('fr', App::getLocale());
    }

    public function acceptLanguageProvider()
    {
        return [
            // header, language that should be selected, default language
            ['it-IT,en-us,en;q=0.5', 'it', 'en'],
            ['it,en-us,en;q=0.5', 'it', 'en'],
            ['en;q=0.5,it;q=0.8', 'it', 'en'],
            ['fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5', 'fr', 'en'],
            ['fr-CH, fr;q=0.9, en;q=0.8, de;q=0.9, *;q=0.5', 'fr', 'en'],
            ['fr-CH, fr;q=0.9, en;q=0.8, de;q=1.0, *;q=0.5', 'fr', 'en'],
            ['', 'en', 'en'],
            [null, 'en', 'en'],
            ['1,2,3', 'en', 'en'],
        ];
    }

    /**
     * @dataProvider acceptLanguageProvider
     */
    public function test_browser_language_is_selected_if_user_do_not_have_preference($header, $expected_language, $default_language)
    {
        config(['app.locale' => $default_language]);

        $user = factory('KBox\User')->create();

        $user->setOption(User::OPTION_LANGUAGE, null);

        $this->be($user);

        $request = Request::create(
            'http://example.com/',
            'GET',
            [],
            [],
            [],
            [
                'HTTP_ACCEPT_LANGUAGE' => $header,
            ]
        );

        $middleware = new Locale();

        $next = new class {
            // anonymous invokable class to track a state for the next closure
            public $called = false;

            public function __invoke($args)
            {
                $this->called = true;
                return $args;
            }
        };

        $response = $middleware->handle($request, $next);

        $this->assertTrue($next->called);
        $this->assertSame($response, $request);
        $this->assertEquals($expected_language, App::getLocale());
    }
}
