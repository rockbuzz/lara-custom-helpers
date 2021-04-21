<?php

declare(strict_types = 1);

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

if (!function_exists('sum_times')) {
    function sum_times(...$times)
    {

        $minutes = 0;
        foreach ($times as $time) {
            if (!has_time_with_seconds($time)) {
                throw new InvalidArgumentException();
            }
            list($hour, $minute) = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}

if (!function_exists('has_time_with_seconds')) {

    function has_time_with_seconds(string $time): bool
    {
        return (bool)preg_match("/^(\d{2}):(\d{2})$/", $time);
    }
}

if (!function_exists('has_br_datetime_format')) {

    function has_br_datetime_format(string $date, bool $withSeconds = false)
    {
        if ($withSeconds) {
            return (bool)preg_match("/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})$/", $date);
        }

        return (bool)preg_match("/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/", $date);
    }
}

if (!function_exists('has_br_date_format')) {

    function has_br_date_format(string $date): bool
    {
        return (bool)preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/", $date);
    }
}

if (!function_exists('has_us_datetime_format')) {

    function has_us_datetime_format(string $date, bool $withSeconds = false): bool
    {
        if ($withSeconds) {
            return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $date);
        }

        return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/", $date);
    }
}

if (!function_exists('has_us_datetime_local_format')) {

    function has_us_datetime_local_format(string $date, bool $withSeconds = false): bool
    {
        if ($withSeconds) {
            return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})$/", $date);
        }

        return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/", $date);
    }
}

if (!function_exists('has_us_date_format')) {

    function has_us_date_format(string $date): bool
    {
        return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date);
    }
}

if (!function_exists('create_us_date_time')) {

    function create_us_date_time(string $date, bool $withSeconds = false): ?Carbon
    {
        if (has_us_date_format($date) ||
            has_us_datetime_format($date, $withSeconds) ||
            has_us_datetime_local_format($date, $withSeconds)) {
            return new Carbon($date);
        }

        if (has_br_date_format($date)) {
            return Carbon::createFromFormat('d/m/Y', $date);
        }

        if (has_br_datetime_format($date, $withSeconds)) {
            $format = $withSeconds ? 'd/m/Y H:i:s' : 'd/m/Y H:i';
            return Carbon::createFromFormat($format, $date);
        }

        return null;
    }
}

if (!function_exists('getIcon_by_mime')) {
    function getIcon_by_mime(string $mime, string $class = null)
    {
        switch ($mime) {
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
            case 'image/svg+xml':
                return 'fa fa-image text-warning ' . $class;
                break;
            case 'application/pdf':
                return 'fa fa-file-pdf text-danger ' . $class;
                break;
            case 'application/msword':
                return 'fa fa-file-word text-primary ' . $class;
                break;
            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'text/csv':
            case 'text/plain':
                return 'fa fa-file-excel text-success ' . $class;
                break;
            default:
                return 'fa fa-file-alt ' . $class;
                break;
        }
    }
}

if (!function_exists('is_current_route')) {
    function is_current_route($routeName): bool
    {
        $currentRoute = Route::currentRouteName();

        if (is_string($routeName)) {
            return Str::contains($currentRoute, $routeName);
        }

        if (is_array($routeName)) {
            return collect($routeName)->reduce(function ($accumulator, $routeName) use ($currentRoute) {
                return !$accumulator ? Str::contains($currentRoute, $routeName) : $accumulator;
            }, false);
        }

        return false;
    }
}

if (!function_exists('active_by_url')) {
    function active_by_url(string $url, string $class = 'active'): string
    {
        return request()->fullUrlIs($url) ? $class : '';
    }
}

if (!function_exists('active_by_route')) {
    function active_by_route($routeName, string $class = 'active'): string
    {
        return is_current_route($routeName) ? $class : '';
    }
}

if (!function_exists('mask')) {

    function mask(string $value, string $mask, string $character = '#'): string
    {

        if (empty($value)) {
            return '';
        }

        $masked = '';

        $key = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == $character) {
                if (isset($value[$key])) {
                    $masked .= $value[$key++];
                }
                continue;
            }

            if (isset($mask[$i])) {
                $masked .= $mask[$i];
            }
        }
        return $masked;
    }
}

if (!function_exists('unmask')) {
    
    function unmask(string $value, array $characters): string
    {
        
        if (empty($value)) {
            return '';
        }

        return str_replace($characters, '', $value);
    }
}

if (!function_exists('display_active')) {

    function display_active(bool $active, string $class = 'label'): string
    {

        list ($type, $content) = $active ?
            [$type = 'success', $content = 'Sim'] :
            [$type = 'danger', $content = 'Não'];

        return sprintf("<small class='%s %s-%s'>%s</small>", $class, $class, $type, $content);
    }
}

if (!function_exists('string_title')) {

    function string_title(string $value): string
    {

        return Str::title($value);
    }
}

if (!function_exists('string_limit')) {

    function string_limit(string $value, int $limit = 100, string $end = '...')
    {

        return Str::limit($value, $limit, $end);
    }
}

if (!function_exists('string_slug')) {

    function string_slug($title, $separator = '-', $language = 'en')
    {

        return Str::slug($title, $separator, $language);
    }
}

if (!function_exists('convert_cents_to_money')) {

    function convert_cents_to_money(int $cents, $prefix = '')
    {

        $value = number_format($cents / 100, 2, ',', '.');

        return "{$prefix}{$value}";
    }
}
 
if (!function_exists('convert_money_to_cents')) {

    function convert_money_to_cents(string $money, $prefix = '')
    {

        if (!is_numeric($value = preg_replace('/[^0-9]/', '', $money))) {
            throw new InvalidArgumentException("The $value must be a number");
        }

        return "{$prefix}{$value}";
    }
}
