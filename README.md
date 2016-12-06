# A Laravel API Wrapper For STAT Search Analytics.

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

- [Introduction](#introduction)
- [Projects Methods](#projects-methods)
    - [List All Projects](#projects-list)
    - [Create A Projects](#projects-create)
    - [Update A Projects](#projects-update)
    - [Delete A Projects](#projects-delete)


<a name="introduction"></a>
## Introduction

The responses of this packages are combinations of Laravel Collections, objects for each kind of data object and [Carbon](https://github.com/briannesbitt/Carbon) instances for date fields.

<a name="projects-methods"></a>
## Projects Methods

<a name="projects-list"></a>
### List All Projects
The list methods returns a collection of all projects.
``` php
$projects = Stat::projects()->list();
```

<a name="projects-create"></a>
### Create A Project
To create a project just pass the name of the project. The response will we a StatProject instance.
``` php
$project = Stat::projects()->create('Cheese Cake Factory');

$project->toArray();

/*
[
    'id' => 615,
    'name' => 'Cheese Cake Factory',
    'total_sites' => 0,
    'created_at' => 2016-11-01,
    'updated_at' => 2016-11-01,
]
*/
```

<a name="projects-update"></a>
### Update A Project
To update the name of a project just pass the project ID and the new name. The response will we a StatProject instance.
``` php
$project = Stat::projects()->update(615, 'Cheese Cake Bakery');

$project->toArray();

/*
[
    'id' => 615,
    'name' => 'Cheese Cake Bakery',
    'total_sites' => 5,
    'created_at' => 2016-11-01,
    'updated_at' => 2016-11-03,
]
*/
```

<a name="projects-delete"></a>
### Delete A Project
To delete a project just pass the project ID. The response the project ID.
``` php
$project = Stat::projects()->delete(615);
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email githubissues@schulze.co instead of using the issue tracker.

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
