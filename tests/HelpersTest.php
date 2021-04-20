<?php

namespace Tests;

use Carbon\Carbon;
use InvalidArgumentException;
use Illuminate\Support\Facades\Route;

class HelpersTest extends TestCase
{
    /** @test */
    public function string_title()
    {
        $this->assertEquals('Test Title', string_title('test title'));
    }

    /** @test */
    public function string_limit()
    {
        $this->assertEquals('Test description limit', string_limit('Test description limit'));
        $this->assertEquals('Test description...', string_limit('Test description limit', 16));
        $this->assertEquals('Test description :)', string_limit('Test description limit', 16, ' :)'));
    }

    /** @test */
    public function string_slug()
    {
        $this->assertEquals('test-slug', string_slug('Test slug'));
        $this->assertEquals('test+slug', string_slug('Test slug', '+'));
    }

    /** @test */
    public function convert_money_in_cents()
    {
        $this->assertEquals(5421, convert_money_to_cents('54,21'));
        $this->assertEquals(542100, convert_money_to_cents('5.421,00'));
        $this->assertEquals(-542100, convert_money_to_cents('5.421,00', '-'));

        $this->expectException(InvalidArgumentException::class);

        convert_money_to_cents('not is numeric');
    }

    /** @test */
    public function convert_cents_to_money()
    {
        $this->assertEquals('54,21', convert_cents_to_money(5421));
        $this->assertEquals('5.421,00', convert_cents_to_money(542100));
        $this->assertEquals('R$ -5.421,00', convert_cents_to_money(542100, 'R$ -'));
    }

    /** @test */
    public function display_active()
    {
        $this->assertEquals(
            "<small class='label label-success'>Sim</small>",
            display_active(true)
        );
        $this->assertEquals(
            "<small class='label label-danger'>Não</small>",
            display_active(false)
        );
        $this->assertEquals(
            "<small class='badge badge-danger'>Não</small>",
            display_active(false, 'badge')
        );
    }

    /** @test */
    public function mask()
    {
        $this->assertEquals('123.456.789-00', mask('12345678900', '###.###.###-##'));
        $this->assertEquals('123.456.789-00', mask('12345678900', '+++.+++.+++-++', '+'));
        $this->assertEquals('', mask('', '###.###.###-##'));
    }

    /** @test */
    public function unmask()
    {
        $this->assertEquals('12345678900', unmask('123.456.789-00', ['.', '-']));
        $this->assertEquals('', unmask('', ['.', '-']));
    }

    /** @test */
    public function is_current_route()
    {
        Route::get('test', function () {
        })->name('test.route');

        $this->get('test');

        $this->assertFalse(is_current_route('any'));
        $this->assertFalse(is_current_route(999));
        $this->assertTrue(is_current_route('test.route'));
        $this->assertTrue(is_current_route(['any', 'test.route']));
    }

    /** @test */
    public function active_by_route()
    {
        Route::get('test', function () {
        })->name('test.route');

        $this->get('test');

        $this->assertEquals('', active_by_route('any'));
        $this->assertEquals('active', active_by_route('test.route'));
        $this->assertEquals('other_class', active_by_route('test.route', 'other_class'));
    }

    /** @test */
    public function active_by_url()
    {
        Route::get('test', function () {
        })->name('test.route');

        $this->get('test');

        $this->assertEquals('', active_by_url('any'));
        $this->assertEquals('active', active_by_url(config('app.url') . '/test'));
        $this->assertEquals('other_class', active_by_url(config('app.url') . '/test', 'other_class'));
    }

    /** @test */
    public function getIcon_by_mime()
    {
        $this->assertEquals('fa fa-image text-warning ', getIcon_by_mime('image/jpeg'));
        $this->assertEquals('fa fa-image text-warning ', getIcon_by_mime('image/png'));
        $this->assertEquals('fa fa-image text-warning ', getIcon_by_mime('image/gif'));
        $this->assertEquals('fa fa-image text-warning ', getIcon_by_mime('image/svg+xml'));

        $this->assertEquals('fa fa-file-pdf text-danger ', getIcon_by_mime('application/pdf'));

        $this->assertEquals('fa fa-file-word text-primary ', getIcon_by_mime('application/msword'));

        $this->assertEquals('fa fa-file-excel text-success ', getIcon_by_mime('application/vnd.ms-excel'));
        $this->assertEquals(
            'fa fa-file-excel text-success ',
            getIcon_by_mime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        );
        $this->assertEquals('fa fa-file-excel text-success ', getIcon_by_mime('text/csv'));
        $this->assertEquals('fa fa-file-excel text-success ', getIcon_by_mime('text/plain'));

        $this->assertEquals('fa fa-file-alt ', getIcon_by_mime('any_mime'));
    }

    /** @test */
    public function has_us_date_format()
    {
        $this->assertFalse(has_us_date_format('10-10-2020'));
        $this->assertTrue(has_us_date_format('2020-10-10'));
    }

    /** @test */
    public function has_us_datetime_local_format()
    {
        $this->assertFalse(has_us_datetime_local_format('10-10-2020'));
        $this->assertTrue(has_us_datetime_local_format('2020-10-10T00:00'));
        $this->assertTrue(has_us_datetime_local_format('2020-10-10T00:00:00', true));
    }

    /** @test */
    public function has_us_datetime_format()
    {
        $this->assertFalse(has_us_datetime_format('10-10-2020'));
        $this->assertTrue(has_us_datetime_format('2020-10-10 00:00'));
        $this->assertTrue(has_us_datetime_format('2020-10-10 00:00:00', true));
    }

    /** @test */
    public function has_br_date_format()
    {
        $this->assertFalse(has_br_date_format('10-10-2020'));
        $this->assertTrue(has_br_date_format('10/10/2020'));
    }

    /** @test */
    public function has_br_datetime_format()
    {
        $this->assertFalse(has_br_datetime_format('10/10/2020'));
        $this->assertTrue(has_br_datetime_format('10/10/2020 00:00'));
        $this->assertTrue(has_br_datetime_format('10/10/2020 00:00:00', true));
    }

    /** @test */
    public function create_us_date_time()
    {
        $this->assertNull(create_us_date_time('any_format'));
        $this->assertInstanceOf(Carbon::class, create_us_date_time('2020-10-10'));
        $this->assertInstanceOf(Carbon::class, create_us_date_time('10/10/2020'));
        $this->assertInstanceOf(Carbon::class, create_us_date_time('10/10/2020 00:00'));
    }

    /** @test */
    public function sum_times()
    {
        $this->assertEquals('12:30', sum_times('06:15', '06:15'));
    }
}
