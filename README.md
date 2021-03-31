# Lara Custom Helpers

Laravel custom helpers

## Requirements

PHP: >=7.2

## Install

```bash
$ composer require rockbuzz/lara-custom-helpers
```

## Usage
```php
get_video_embed_url(string $url, array $options = []);
image(string $filePatch, string $template = null);
sum_times(...$times);
visite_increment($model, $column = 'view', $viewedKey = 'viewed_post');
has_br_datetime_format(string $date, bool $withSeconds = false);
has_br_date_format(string $date);
has_us_datetime_format(string $date, bool $withSeconds = false);
has_us_datetime_local_format(string $date, bool $withSeconds = false);
has_us_date_format(string $date);
create_us_date_time(string $date = null, bool $withSeconds = false): ?Carbon;
getIcon_by_mime(string $mime, string $class = null);
is_current_route($routeName): bool;
active_by_url(string $url, string $class = 'active'): string;
active_by_route($routeName, string $class = 'active'): string;
mask($value, string $mask): string;
unmask(string $value, array $unmask): string;
display_active($active, string $classPrefix = 'label'): string;
string_title(string $value): string;
string_limit(string $value, int $limit = 100, string $end = '...');
string_slug($title, $separator = '-', $language = 'en');
convert_to_brl($moneyInCents, $prefix = null);
convert_to_money($moneyInCents);
convert_money_in_cents($money);
```

## License

The Lara Custom Helpers is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).