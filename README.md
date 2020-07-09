# Production Monitoring System

## Installation

After Git cloning do a composer update command.

```
$ composer update
```

Make a .env file. Execute this command.

```
$ cp .env.example .env
```

or

```
$ copy .env.example .env
```

Then setup your .env file and your desired database.

After setting up execute two php artisan command.

```
$ php artisan key:generate
```

```
$ php artisan config:cache
```

Copy files in z-customize_laravel folder and paste it to:

```
/vendor/laravel/framework/src/Illuminate/Foundation/Auth
```