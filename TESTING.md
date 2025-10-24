# Testing Documentation

This document provides comprehensive information about the test suite for the Laravel Telegram Bot package.

## Overview

The test suite is built using **Pest PHP**, a delightful PHP testing framework with a focus on simplicity. The tests ensure that all core functionality of the Telegram bot package works correctly.

## Test Structure

```
tests/
├── Helpers/
│   └── TelegramMocker.php          # Helper for mocking Telegram API responses
├── Unit/
│   ├── Abstract/
│   │   └── DTOTest.php             # Tests for base DTO class
│   ├── Models/
│   │   ├── TelegramBotTest.php     # Tests for TelegramBot model
│   │   ├── TelegramChatTest.php    # Tests for TelegramChat model
│   │   └── TelegramUserTest.php    # Tests for TelegramUser model
│   ├── APITest.php                 # Tests for main API class
│   ├── ChatAPITest.php             # Tests for ChatAPI class
│   └── StorageTest.php             # Tests for Storage class
├── Feature/
│   └── (Feature tests will go here)
├── TestCase.php                    # Base test case class
├── Pest.php                        # Pest configuration
└── ArchTest.php                    # Architecture tests
```

## Running Tests

### Run All Tests

```bash
composer test
```

### Run Specific Test File

```bash
composer test tests/Unit/StorageTest.php
```

### Run Tests with Coverage

```bash
composer test-coverage
```

### Run Tests with Filter

```bash
composer test --filter="Storage"
```

### Run Tests in Parallel

```bash
composer test --parallel
```

## Test Categories

### Unit Tests

Unit tests focus on testing individual classes and methods in isolation.

#### Abstract/DTO Tests
- **File**: `tests/Unit/Abstract/DTOTest.php`
- **Coverage**:
  - DTO instantiation and validation
  - Nested attribute access with dot notation
  - Array conversion
  - Required field validation
  - Error handling

#### Storage Tests
- **File**: `tests/Unit/StorageTest.php`
- **Coverage**:
  - Simple key-value storage
  - Nested data with dot notation
  - Data type preservation
  - Multiple storage instances
  - Forget/delete operations

#### Model Tests

##### TelegramBot Model
- **File**: `tests/Unit/Models/TelegramBotTest.php`
- **Coverage**:
  - Model creation and attributes
  - JSON casting for `get_me` field
  - API instance creation
  - Relationships with TelegramChat
  - Database queries

##### TelegramChat Model
- **File**: `tests/Unit/Models/TelegramChatTest.php`
- **Coverage**:
  - Model creation with all attributes
  - JSON/Collection casting
  - DateTime casting for live mode fields
  - Relationships with TelegramBot
  - ChatAPI instance creation

##### TelegramUser Model
- **File**: `tests/Unit/Models/TelegramUserTest.php`
- **Coverage**:
  - Polymorphic relationships
  - User linking to authenticatable models
  - Multiple users per chat
  - Database operations

#### API Tests

##### Main API
- **File**: `tests/Unit/APITest.php`
- **Coverage**:
  - `getMe()` - Bot information retrieval
  - `setWebhook()` / `deleteWebhook()` - Webhook management
  - `getUpdates()` - Polling with parameters
  - `setMyCommands()` - Bot command setup
  - `setMyName/Description()` - Bot metadata
  - `answerCallbackQuery()` - Callback handling
  - `getFileLink()` - File download links
  - Error handling

##### Chat API
- **File**: `tests/Unit/ChatAPITest.php`
- **Coverage**:
  - Text message sending
  - Messages with inline keyboards
  - Chat actions (typing, uploading, etc.)
  - Message deletion (single and bulk)
  - User profile photos

### Feature Tests

Feature tests will test complete workflows and integrations (to be implemented).

### Architecture Tests
- **File**: `tests/ArchTest.php`
- **Purpose**: Ensures code quality standards
- **Checks**: No debugging functions (dd, dump, ray) in production code

## Test Helpers

### TelegramMocker

The `TelegramMocker` class provides convenient methods for mocking Telegram API responses:

```php
use sakoora0x\Telegram\Tests\Helpers\TelegramMocker;

// Mock successful API response
TelegramMocker::fakeSuccessResponse(['key' => 'value']);

// Mock error response
TelegramMocker::fakeErrorResponse('Error message', 400);

// Mock getMe response
TelegramMocker::fakeGetMeResponse(['username' => 'test_bot']);

// Mock webhook responses
TelegramMocker::fakeWebhookResponse(true);

// Mock message sending
TelegramMocker::fakeSendMessageResponse(['text' => 'Message sent']);

// Mock getUpdates
TelegramMocker::fakeGetUpdatesResponse([/* updates array */]);

// Mock all Telegram APIs
TelegramMocker::fakeAllTelegramApis();

// Create update payload for testing
$payload = TelegramMocker::createUpdatePayload([
    'message' => ['text' => '/start']
]);

// Create callback query payload
$payload = TelegramMocker::createCallbackQueryPayload([
    'data' => 'button_clicked'
]);
```

## Database Setup

Tests use an in-memory SQLite database for fast and isolated testing. The `TestCase` class automatically:

1. Configures SQLite in-memory database
2. Runs package migrations before each test
3. Cleans up after each test

## Writing New Tests

### Unit Test Example

```php
<?php

use YourNamespace\YourClass;

describe('YourClass', function () {
    beforeEach(function () {
        $this->instance = new YourClass();
    });

    it('does something correctly', function () {
        $result = $this->instance->doSomething();

        expect($result)->toBe('expected value');
    });

    it('throws exception on invalid input', function () {
        $this->instance->methodThatThrows();
    })->throws(Exception::class, 'Error message');
});
```

### Feature Test Example

```php
<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Feature', function () {
    it('handles complete workflow', function () {
        // Arrange
        $bot = TelegramBot::create([...]);

        // Act
        $response = $this->post('/webhook', $payload);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('telegram_chats', [...]);
    });
});
```

## Test Coverage Goals

Current test coverage focuses on:

- ✅ Core DTO functionality (100%)
- ✅ Storage operations (95%)
- ✅ Database models (80%)
- ✅ API communication (75%)
- ⏳ HTML Parser (0% - pending)
- ⏳ Message Rendering (0% - pending)
- ⏳ Form validation (0% - pending)
- ⏳ Authentication guard (0% - pending)
- ⏳ Webhook handling (0% - pending)

## Continuous Integration

To integrate with CI/CD:

### GitHub Actions Example

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, sqlite3

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run Tests
        run: composer test

      - name: Run Coverage
        run: composer test-coverage
```

## Best Practices

1. **Isolation**: Each test should be independent and not rely on other tests
2. **Clarity**: Test names should clearly describe what is being tested
3. **Arrange-Act-Assert**: Structure tests with clear setup, execution, and verification
4. **Mocking**: Use `TelegramMocker` to avoid real API calls
5. **Database**: Use factories and seeders for consistent test data
6. **Speed**: Keep tests fast by using in-memory databases and mocking external services

## Troubleshooting

### Common Issues

#### Migration Errors
If you see "table already exists" errors:
```bash
# Clear test cache
php artisan config:clear
php artisan cache:clear
```

#### HTTP Mocking Issues
Ensure you're using `Http::fake()` before making API calls:
```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'https://api.telegram.org/*' => Http::response(['ok' => true])
]);
```

#### Database Connection Issues
Verify SQLite extension is installed:
```bash
php -m | grep sqlite
```

## Future Improvements

- [ ] Add more feature tests for complete workflows
- [ ] Implement HTMLParser tests
- [ ] Add TelegramRender tests
- [ ] Create Form validation tests
- [ ] Test WebhookHandler thoroughly
- [ ] Add TelegramGuard authentication tests
- [ ] Increase code coverage to 90%+
- [ ] Add mutation testing
- [ ] Performance benchmarks

## Contributing

When adding new features:

1. Write tests first (TDD approach)
2. Ensure all existing tests pass
3. Add documentation for new test helpers
4. Update this README with new test files
5. Maintain or improve code coverage

## Resources

- [Pest PHP Documentation](https://pestphp.com/)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [Telegram Bot API](https://core.telegram.org/bots/api)
