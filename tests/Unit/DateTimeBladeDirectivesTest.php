<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Facades\Blade;
use Jenssegers\Date\Date;

class DateTimeBladeDirectivesTest extends TestCase
{
    public function test_as_localizable_date_macro()
    {
        $localizableDate = Carbon::now()->asLocalizableDate();

        $this->assertInstanceOf(Date::class, $localizableDate);
    }
    
    public function test_render_date_macro()
    {
        $dateString = Carbon::createFromDate(2019, 10, 3)->render();

        $this->assertEquals('3 October 2019', $dateString);
    }
    
    public function test_render_datetime_macro()
    {
        $dateTimeString = Carbon::create(2019, 10, 3, 7, 29, 0)->render(true);

        $this->assertEquals('3 October 2019 07:29 (UTC)', $dateTimeString);
    }
    
    public function test_date_directives_are_registered()
    {
        $directives = Blade::getCustomDirectives();

        $this->assertArrayHasKey('date', $directives);
        $this->assertArrayHasKey('datetime', $directives);
    }
}
