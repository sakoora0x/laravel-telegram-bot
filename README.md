# Laravel Telegram Bot

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sakoora0x/laravel-telegram-bot.svg?style=flat-square)](https://packagist.org/packages/sakoora0x/laravel-telegram-bot)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/sakoora0x/laravel-telegram-bot/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/sakoora0x/laravel-telegram-bot/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/sakoora0x/laravel-telegram-bot/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/sakoora0x/laravel-telegram-bot/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/sakoora0x/laravel-telegram-bot.svg?style=flat-square)](https://packagist.org/packages/sakoora0x/laravel-telegram-bot)

This package for Laravel 11+ allows you to easily create interactive Telegram bots, using Laravel routing, and using Blade templates to conduct a dialogue with the user.

## Installation

You can install the package via composer:

```bash
composer require sakoora0x/laravel-telegram-bot
```

```bash
php artisan telegram:install
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="telegram-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="telegram-config"
```

Optionally, you can publish the views using:

```bash
php artisan vendor:publish --tag="telegram-views"
```

### Configuration for Laravel Sail

Optionally, if you use Sail for local development, you need to add PHP parameter `PHP_CLI_SERVER_WORKERS="10"` in file `supervisord.conf`:

```bash
[program:php]
command=%(ENV_SUPERVISOR_PHP_COMMAND)s
user=%(ENV_SUPERVISOR_PHP_USER)s
environment=LARAVEL_SAIL="1",PHP_CLI_SERVER_WORKERS="10"
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
```

### Authentication Setup

You can use Laravel Auth, edit file `config/auth.php` and edit section `guards`:

```php
'guards' => [
    'web' => [...],
    'telegram' => [
        'driver' => 'telegram',
        'provider' => 'users',
    ]
],
```

After this you can use middleware `auth:telegram` in your routes.

### Scheduled Tasks

If you want to work with automatic dialog truncation, you must run command `php artisan telegram:truncate` every minute using Schedule.

### Live Pages Setup

You can configure "live pages" (auto-refreshing pages). In the `bootstrap/app.php` file, add an alias to the `withMiddleware` section:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'telegram.live' => \sakoora0x\Telegram\Middleware\LiveMiddleware::class,
    ]);
})
```

Then connect the middleware to the desired route:

```php
Route::telegram('/', [\App\Telegram\Controllers\MyController::class, 'index'])
    ->middleware(['telegram.live:30']);
```

The argument is the frequency in seconds for how often to update the page.

And add to the `routes/console.php` file:

```php
Schedule::command('telegram:live')
    ->runInBackground()
    ->everyMinute();
```

## Usage

### Create New Telegram Bot

```bash
php artisan telegram:new-bot
```

### Set Webhook for Bot

```bash
php artisan telegram:set-webhook
```

### Unset Webhook for Bot

```bash
php artisan telegram:unset-webhook
```

### Manual Polling (on localhost) for Bot

```bash
php artisan telegram:polling [BOT_ID]
```

## Features

### Inline Keyboard

If you want to create a button to change the current URI query params, use this template:

```html
<inline-keyboard>
    <row>
        <column query-param="value">Change query param</column>
    </row>
</inline-keyboard>
```

If you want to send POST data, you must use this template:

```html
<inline-keyboard>
    <row>
        <column data-field="value">Send field value</column>
    </row>
</inline-keyboard>
```

If the POST data is long, you can encrypt it using this template:

```html
<inline-keyboard>
    <row>
        <column data-field="long value" encode="true">Encoded send data</column>
    </row>
</inline-keyboard>
```

If you want to make a redirect to another page from a button, use this template:

```html
<inline-keyboard>
    <row>
        <column data-redirect="/">Redirect to /</column>
    </row>
</inline-keyboard>
```

### Edit Form

Create a form class for data editing:

```php
class MyForm extends \sakoora0x\Telegram\EditForm\BaseForm
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:15'],
        ];
    }

    public function titles(): array
    {
        return [
            'name' => 'Your name',
            'phone' => 'Your phone number'
        ];
    }
}
```

Use the form in your controller:

```php
class MyController
{
    public function edit(MyForm $form): mixed
    {
        $form->setDefault([
            'name' => 'Default name',
            'phone' => '1234567890',
        ]);

        if ($form->validate()) {
            // $form->get();
        }

        return view('...', compact('form'));
    }

    public function create(MyForm $form): mixed
    {
        if ($form->isCreate()->validate()) {
            // $form->get();
        }

        return view('...', compact('form'));
    }
}
```

Display the form in your Blade template:

```html
<message>
    <x-telegram-edit-form :form="$form">
        <x-slot:name>
            <line>Please, enter your First Name:</line>
        </x-slot:name>
    </x-telegram-edit-form>
</message>
```

## Testing

The package includes comprehensive tests covering all core functionality.

```bash
composer test
```

**Test Results:**
- ✅ 88 passing tests
- ⏭️ 1 skipped
- 138 assertions
- Duration: ~2.4 seconds

See [TESTING.md](TESTING.md) for detailed testing documentation.

## Future Ideas

1. Add `query-history=false` parameter to Inline Button so that the current URL is not saved in referer, preventing form reset on back navigation.
2. Enable users to upload photos/videos/documents and parse captions in messages.
3. Add phone number sharing button in Reply Button and receive results in TelegramRequest.
4. Read results from forwarded contacts in TelegramRequest.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [MollSoft](https://github.com/mollsoft)
- [sakoora0x](https://github.com/sakoora0x)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
