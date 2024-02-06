# Laravel Forum Package

An easy to integrate forum to your laravel project. Just customize views, migrations routes and you are done.

## Installation

You can install the package via composer:

```bash
composer require dimimo/pool-forum
```

You could publish migrations, views & config

```bash
php artisan vendor:publish --provider="Dimimo\PoolForum\PoolForumServiceProvider"
```

## Configuration

```php
//config/pool-forum.php

/*
 * Customize table table names to your needs
 */
return [
    'table_names' => [
        'settings' => 'settings',
    ]
];
```

## Usage

---

```php
//routes/web.php

use Dimimo\PoolForum\PoolForumFacade as PoolForum;

Route::middleware(['auth'])->prefix('forum')->group(function () {
    PoolForum::routes();
});
```

```php
//routes/api.php
use Dimimo\PoolForum\PoolForumFacade as PoolForum;

Route::middleware(['auth'])->prefix('forum')->group(function () {
    PoolForum::routes();
});
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email victoryoalli@gmail.com instead of using the issue tracker.

## Credits

-   [Victor Yoalli](https://github.com/vientodigital)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
