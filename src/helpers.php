<?php

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

if (!function_exists('get_video_embed_url')) {
    function get_video_embed_url(string $url, array $options = []) {
        $default = asset('images/abtd.png');

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return $default;
        }

        $query = http_build_query($options);
        $domain = str_replace('www.', '', parse_url($url, PHP_URL_HOST));

        switch ($domain) {
            case 'vimeo.com':
                $path = parse_url($url, PHP_URL_PATH);
                return "https://player.vimeo.com/video{$path}" . (!empty($options) ? "?{$query}" : '');
            case 'player.vimeo.com':
                return $url;
            case 'youtube.com':
            case 'youtube.com.br':
            default:
                if (Str::contains($url, '/embed/')) {
                    return $url;
                }

                parse_str(Str::after($url, '?'), $params);

                if (empty($params['v'])) {
                    return $default;
                }

                return "https://youtube.com/embed/{$params['v']}" . (!empty($options) ? "?{$query}" : '');
        }
    }
}

if (!function_exists('image')) {
    function image(string $filePatch, string $template = null) {
        $template = $template ?? 'default';

        if (!config("imagecache.templates.{$template}")) {
            return asset($filePatch);
        }

        if (Str::startsWith($filePatch, '/')) {
            $filePatch = Str::replaceFirst('/', '', $filePatch);
        }

        return route('imagecache', [$template, $filePatch]);
    }
}

if (!function_exists('sum_times')) {
    function sum_times(...$times) {
        $minutes = 0;
        foreach ($times as $time) {
            list($hour, $minute) = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;

        return sprintf('%02d:%02d', $hours, $minutes);
    }
}

if (!function_exists('visite_increment')) {
    function visite_increment($model, $column = 'view', $viewedKey = 'viewed_post') {
        $viewed = session()->get($viewedKey, []);
        if (!in_array($model->id, $viewed)) {
            $model->increment($column);
            session()->push($viewedKey, $model->id);
        }
    }
}

if (!function_exists('has_br_datetime_format')) {

    function has_br_datetime_format(string $date, bool $withSeconds = false) {
        if ($withSeconds) {
            return (bool)preg_match("/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}):(\d{2})$/", $date);
        }

        return (bool)preg_match("/^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/", $date);
    }
}

if (!function_exists('has_br_date_format')) {

    function has_br_date_format(string $date) {
        return (bool)preg_match("/^(\d{2})\/(\d{2})\/(\d{4})$/", $date);
    }
}

if (!function_exists('has_us_datetime_format')) {

    function has_us_datetime_format(string $date, bool $withSeconds = false) {
        if ($withSeconds) {
            return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/", $date);
        }

        return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})$/", $date);
    }
}

if (!function_exists('has_us_datetime_local_format')) {

    function has_us_datetime_local_format(string $date, bool $withSeconds = false) {
        if ($withSeconds) {
            return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})$/", $date);
        }

        return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/", $date);
    }
}

if (!function_exists('has_us_date_format')) {

    function has_us_date_format(string $date) {
        return (bool)preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date);
    }
}

if (!function_exists('create_us_date_time')) {

    function create_us_date_time(string $date = null, bool $withSeconds = false): ?Carbon {
        if (!$date) {
            return null;
        }

        if (has_us_date_format($date) ||
            has_us_datetime_format($date, $withSeconds) ||
            has_us_datetime_local_format($date, $withSeconds)) {
            return (new Carbon($date));
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
    function getIcon_by_mime(string $mime, string $class = null) {
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
    function is_current_route($routeName): bool {
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
    function active_by_url(string $url, string $class = 'active'): string {
        return request()->fullUrlIs($url) ? $class : '';
    }
}

if (!function_exists('active_by_route')) {
    function active_by_route($routeName, string $class = 'active'): string {
        return is_current_route($routeName) ? $class : '';
    }
}

if (!function_exists('mask')) {
    function mask($value, string $mask): string {
        $masked = '';

        if (!empty($value)) {
            $key = 0;
            for ($i = 0; $i <= strlen($mask) - 1; $i++) {
                if ($mask[$i] == '#') {
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

        return '';
    }
}

if (!function_exists('unmask')) {
    function unmask(string $value, array $unmask): string {
        if (empty($value)) {
            return '';
        }
        return str_replace($unmask, '', $value);
    }
}

if (!function_exists('display_active')) {
    function display_active($active, string $classPrefix = 'label'): string {
        if ($active) {
            return "<small class='{$classPrefix} {$classPrefix}-success'>Sim</small>";
        }

        return "<small class='{$classPrefix} {$classPrefix}-danger'>Não</small>";
    }
}

if (!function_exists('string_title')) {
    function string_title(string $value): string {
        return Str::title($value);
    }

    if (!function_exists('string_limit')) {
        function string_limit(string $value, int $limit = 100, string $end = '...') {
            return Str::limit($value, $limit, $end);
        }
    }
}

if (!function_exists('string_slug')) {
    function string_slug($title, $separator = '-', $language = 'en') {
        return Str::slug($title, $separator, $language);
    }
}

if (!function_exists('convert_to_brl')) {
    function convert_to_brl($moneyInCents, $prefix = null) {
        $money = $moneyInCents / 100;
        return "R$ {$prefix}" . number_format($money, 2, ',', '.');
    }
}

if (!function_exists('convert_to_money')) {
    function convert_to_money($moneyInCents) {
        $money = $moneyInCents / 100;
        return number_format($money, 2, ',', '.');
    }
}
 
if (!function_exists('convert_money_in_cents')) {
    function convert_money_in_cents($money) {
        return preg_replace('/[^0-9]/', '', $money);
    }
}
