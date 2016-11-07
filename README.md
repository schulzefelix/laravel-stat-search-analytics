# API Wrapper For STAT Search Analytics

[![Latest Version](https://img.shields.io/github/release/schulzefelix/laravel-stat-search-analytics.svg?style=flat-square)](https://github.com/schulzefelix/laravel-stat-search-analytics/releases)
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Quality Score][ico-code-quality]][link-code-quality]
[![StyleCI](https://styleci.io/repos/72838426/shield)](https://styleci.io/repos/72838426)
[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

## Install

This package can be installed through Composer.

``` bash
$ composer require schulzefelix/laravel-stat-search-analytics
```

You must install this service provider.
```php
// config/app.php
'providers' => [
    ...
    SchulzeFelix\Stat\StatServiceProvider::class,
    ...
];
```

This package also comes with a facade, which provides an easy way to call the the class.

```php
// config/app.php
'aliases' => [
    ...
    'Stat' => SchulzeFelix\Stat\StatFacade::class,
    ...
];
```


You can publish the config file of this package with this command:

``` bash
php artisan vendor:publish --provider="SchulzeFelix\Stat\StatServiceProvider"
```

## Usage

``` php
$newProject = Stat::projects()->create('Cheese Cake Factory');
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email schulzefelix@example.com instead of using the issue tracker.

## Credits

- [Felix Schulze][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/schulzefelix/laravel-stat-search-analytics.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/schulzefelix/laravel-stat-search-analytics/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/schulzefelix/laravel-stat-search-analytics.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/schulzefelix/laravel-stat-search-analytics.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/schulzefelix/laravel-stat-search-analytics.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/schulzefelix/laravel-stat-search-analytics
[link-travis]: https://travis-ci.org/schulzefelix/laravel-stat-search-analytics
[link-scrutinizer]: https://scrutinizer-ci.com/g/schulzefelix/laravel-stat-search-analytics/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/schulzefelix/laravel-stat-search-analytics
[link-downloads]: https://packagist.org/packages/schulzefelix/laravel-stat-search-analytics
[link-author]: https://github.com/schulzefelix
[link-contributors]: ../../contributors
