<?php

namespace sakoora0x\Telegram\Tests\Helpers;

use Illuminate\Support\Facades\Http;

class TelegramMocker
{
    public static function fakeSuccessResponse(array $result = []): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => true,
                'result' => $result,
            ]),
        ]);
    }

    public static function fakeErrorResponse(string $description = 'Error', int $errorCode = 400): void
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response([
                'ok' => false,
                'error_code' => $errorCode,
                'description' => $description,
            ], $errorCode),
        ]);
    }

    public static function fakeGetMeResponse(array $overrides = []): void
    {
        $defaultBot = [
            'id' => 123456789,
            'is_bot' => true,
            'first_name' => 'TestBot',
            'username' => 'test_bot',
            'can_join_groups' => true,
            'can_read_all_group_messages' => false,
            'supports_inline_queries' => false,
        ];

        Http::fake([
            '*getMe*' => Http::response([
                'ok' => true,
                'result' => array_merge($defaultBot, $overrides),
            ]),
        ]);
    }

    public static function fakeWebhookResponse(bool $success = true): void
    {
        Http::fake([
            '*setWebhook*' => Http::response([
                'ok' => $success,
                'result' => $success,
                'description' => $success ? 'Webhook was set' : 'Failed to set webhook',
            ]),
            '*deleteWebhook*' => Http::response([
                'ok' => $success,
                'result' => $success,
                'description' => $success ? 'Webhook was deleted' : 'Failed to delete webhook',
            ]),
        ]);
    }

    public static function fakeSendMessageResponse(array $overrides = []): void
    {
        $defaultMessage = [
            'message_id' => 123,
            'from' => [
                'id' => 123456789,
                'is_bot' => true,
                'first_name' => 'TestBot',
                'username' => 'test_bot',
            ],
            'chat' => [
                'id' => 987654321,
                'first_name' => 'Test',
                'username' => 'testuser',
                'type' => 'private',
            ],
            'date' => time(),
            'text' => 'Test message',
        ];

        Http::fake([
            '*sendMessage*' => Http::response([
                'ok' => true,
                'result' => array_merge($defaultMessage, $overrides),
            ]),
        ]);
    }

    public static function fakeGetUpdatesResponse(array $updates = []): void
    {
        Http::fake([
            '*getUpdates*' => Http::response([
                'ok' => true,
                'result' => $updates,
            ]),
        ]);
    }

    public static function fakeAllTelegramApis(): void
    {
        Http::fake([
            '*' => Http::response([
                'ok' => true,
                'result' => true,
            ]),
        ]);
    }

    public static function createUpdatePayload(array $overrides = []): array
    {
        $default = [
            'update_id' => 123456789,
            'message' => [
                'message_id' => 1,
                'from' => [
                    'id' => 987654321,
                    'is_bot' => false,
                    'first_name' => 'Test',
                    'username' => 'testuser',
                ],
                'chat' => [
                    'id' => 987654321,
                    'first_name' => 'Test',
                    'username' => 'testuser',
                    'type' => 'private',
                ],
                'date' => time(),
                'text' => '/start',
            ],
        ];

        return array_merge_recursive($default, $overrides);
    }

    public static function createCallbackQueryPayload(array $overrides = []): array
    {
        $default = [
            'update_id' => 123456789,
            'callback_query' => [
                'id' => '123456789',
                'from' => [
                    'id' => 987654321,
                    'is_bot' => false,
                    'first_name' => 'Test',
                    'username' => 'testuser',
                ],
                'message' => [
                    'message_id' => 1,
                    'from' => [
                        'id' => 123456789,
                        'is_bot' => true,
                        'first_name' => 'TestBot',
                        'username' => 'test_bot',
                    ],
                    'chat' => [
                        'id' => 987654321,
                        'first_name' => 'Test',
                        'username' => 'testuser',
                        'type' => 'private',
                    ],
                    'date' => time(),
                    'text' => 'Test message',
                ],
                'chat_instance' => '123456789',
                'data' => 'callback_data',
            ],
        ];

        return array_merge_recursive($default, $overrides);
    }
}
