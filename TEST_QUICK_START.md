# Test Suite Quick Start Guide

Quick reference for running and working with tests in the Laravel Telegram Bot package.

## Run Tests

```bash
# Run all tests
composer test

# Run specific file
composer test tests/Unit/StorageTest.php

# Run with filter
composer test --filter="Storage"

# Run with coverage
composer test-coverage

# Run in parallel
composer test --parallel
```

## Test Status Overview

| Test Suite | File | Tests | Status | Notes |
|-------------|------|-------|--------|-------|
| DTO | `tests/Unit/Abstract/DTOTest.php` | 16 | ✅ Passing | Core DTO functionality |
| Storage | `tests/Unit/StorageTest.php` | 19 | ✅ 18/19 | Cache-based storage |
| TelegramBot | `tests/Unit/Models/TelegramBotTest.php` | 9 | ⚠️ 7/9 | Bot model tests |
| TelegramChat | `tests/Unit/Models/TelegramChatTest.php` | 17 | ⚠️ Most | Chat model tests |
| TelegramUser | `tests/Unit/Models/TelegramUserTest.php` | 10 | ⚠️ Most | User model tests |
| API | `tests/Unit/APITest.php` | 25 | ⚠️ 3/25 | Main API tests |
| ChatAPI | `tests/Unit/ChatAPITest.php` | 9 | ⚠️ Structured | Chat API tests |
| Architecture | `tests/ArchTest.php` | 1 | ✅ Passing | Code quality |

**Legend**: ✅ All passing | ⚠️ Partially passing | ❌ Failing

## Quick Test Examples

### Test with Telegram Mocking

```php
use sakoora0x\Telegram\Tests\Helpers\TelegramMocker;

it('sends a message', function () {
    TelegramMocker::fakeSendMessageResponse();

    $api = new ChatAPI('token', 123456);
    $message = Message::make(['text' => 'Hello']);
    $result = $api->send($message);

    expect($result)->toBeArray();
});
```

### Test Database Models

```php
use sakoora0x\Telegram\Models\TelegramBot;

it('creates a bot', function () {
    $bot = TelegramBot::create([
        'token' => 'test_token',
        'username' => 'test_bot',
    ]);

    expect($bot)->toBeInstanceOf(TelegramBot::class);
    expect($bot->username)->toBe('test_bot');
});
```

### Test Storage

```php
use sakoora0x\Telegram\Storage;

it('stores nested data', function () {
    $storage = new Storage('key');
    $storage->storeData('user.name', 'John');

    expect($storage->retrieveData('user.name'))->toBe('John');
});
```

## TelegramMocker Methods

```php
// Basic mocking
TelegramMocker::fakeSuccessResponse($result);
TelegramMocker::fakeErrorResponse($message, $code);

// Specific endpoints
TelegramMocker::fakeGetMeResponse($botData);
TelegramMocker::fakeWebhookResponse($success);
TelegramMocker::fakeSendMessageResponse($messageData);
TelegramMocker::fakeGetUpdatesResponse($updates);

// Mock everything
TelegramMocker::fakeAllTelegramApis();

// Create test payloads
$update = TelegramMocker::createUpdatePayload($overrides);
$callback = TelegramMocker::createCallbackQueryPayload($overrides);
```

## Common Test Patterns

### Describe Blocks
```php
describe('Feature', function () {
    beforeEach(function () {
        // Setup code
    });

    it('does something', function () {
        // Test code
    });
});
```

### Expectations
```php
expect($value)->toBe($expected);
expect($value)->toBeTrue();
expect($value)->toBeInstanceOf(Class::class);
expect($value)->toBeArray();
expect($value)->toHaveCount(5);
```

### Exceptions
```php
it('throws exception', function () {
    $instance->methodThatThrows();
})->throws(Exception::class, 'Error message');
```

## Troubleshooting

### Tests Failing?
1. Clear cache: `php artisan config:clear`
2. Check database: SQLite must be installed
3. Verify HTTP mocking: Use `Http::fake()` before API calls

### Need More Examples?
- See [TESTING.md](TESTING.md) for detailed guide
- See [TEST_IMPLEMENTATION_SUMMARY.md](TEST_IMPLEMENTATION_SUMMARY.md) for complete overview
- Check existing test files for patterns

## File Structure

```
tests/
├── Helpers/
│   └── TelegramMocker.php    # HTTP mocking helper
├── Unit/
│   ├── Abstract/              # Core classes
│   ├── Models/                # Eloquent models
│   └── *.php                  # API and services
├── Feature/                   # Integration tests (coming soon)
└── TestCase.php               # Base test class
```

## Key Files

- **TestCase.php** - Base test configuration
- **TelegramMocker.php** - HTTP mocking utilities
- **Pest.php** - Pest framework configuration
- **phpunit.xml.dist** - PHPUnit configuration

## Next Steps

1. Run `composer test` to see current status
2. Fix failing model tests (add factories)
3. Fix API HTTP mocking issues
4. Add more feature tests
5. Increase coverage to 80%+

## Resources

- [Pest PHP Docs](https://pestphp.com/)
- [Laravel Testing](https://laravel.com/docs/testing)
- [Telegram Bot API](https://core.telegram.org/bots/api)
