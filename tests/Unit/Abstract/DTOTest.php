<?php

use sakoora0x\Telegram\Abstract\DTO;

// Create a concrete test DTO class
class TestDTO extends DTO
{
    protected function required(): array
    {
        return ['id', 'name'];
    }

    // Expose protected methods for testing
    public function publicGet(string $name, mixed $default = null): mixed
    {
        return $this->get($name, $default);
    }

    public function publicGetOrFail(string $name): mixed
    {
        return $this->getOrFail($name);
    }
}

describe('DTO Abstract Class', function () {
    it('can be instantiated with attributes', function () {
        $dto = new TestDTO(['id' => 1, 'name' => 'Test']);

        expect($dto)->toBeInstanceOf(TestDTO::class);
    });

    it('can create instance using make method without validation', function () {
        $dto = TestDTO::make(['id' => 1, 'name' => 'Test']);

        expect($dto)->toBeInstanceOf(TestDTO::class);
    });

    it('can create instance using fromArray method with validation', function () {
        $dto = TestDTO::fromArray(['id' => 1, 'name' => 'Test']);

        expect($dto)->toBeInstanceOf(TestDTO::class);
    });

    it('throws exception when required attributes are missing with validation', function () {
        TestDTO::fromArray(['id' => 1]);
    })->throws(Exception::class, 'Attribute name is required.');

    it('does not throw exception when validation is disabled', function () {
        $dto = TestDTO::make(['id' => 1], false);

        expect($dto)->toBeInstanceOf(TestDTO::class);
    });

    it('can get attribute using get method', function () {
        $dto = TestDTO::make(['id' => 1, 'name' => 'Test']);

        expect($dto->publicGet('id'))->toBe(1);
        expect($dto->publicGet('name'))->toBe('Test');
    });

    it('returns default value when attribute does not exist', function () {
        $dto = TestDTO::make(['id' => 1]);

        expect($dto->publicGet('missing', 'default'))->toBe('default');
    });

    it('can get nested attributes using dot notation', function () {
        $dto = TestDTO::make([
            'id' => 1,
            'name' => 'Test',
            'user' => [
                'id' => 100,
                'profile' => [
                    'age' => 25,
                ],
            ],
        ]);

        expect($dto->publicGet('user.id'))->toBe(100);
        expect($dto->publicGet('user.profile.age'))->toBe(25);
    });

    it('returns default for missing nested attributes', function () {
        $dto = TestDTO::make(['id' => 1, 'name' => 'Test']);

        expect($dto->publicGet('user.profile.age', 30))->toBe(30);
    });

    it('throws exception when using getOrFail for missing attribute', function () {
        $dto = TestDTO::make(['id' => 1, 'name' => 'Test']);

        $dto->publicGetOrFail('missing');
    })->throws(Exception::class, 'Attribute missing not found.');

    it('can get nested attributes using getOrFail', function () {
        $dto = TestDTO::make([
            'id' => 1,
            'name' => 'Test',
            'user' => ['id' => 100],
        ]);

        expect($dto->publicGetOrFail('user.id'))->toBe(100);
    });

    it('throws exception for missing nested attribute with getOrFail', function () {
        $dto = TestDTO::make(['id' => 1, 'name' => 'Test']);

        $dto->publicGetOrFail('user.profile.age');
    })->throws(Exception::class, 'Attribute user.profile.age not found.');

    it('can convert to array', function () {
        $attributes = ['id' => 1, 'name' => 'Test'];
        $dto = TestDTO::make($attributes);

        expect($dto->toArray())->toBe($attributes);
    });

    it('converts nested DTOs to arrays', function () {
        $nestedDto = TestDTO::make(['id' => 2, 'name' => 'Nested']);
        $dto = TestDTO::make([
            'id' => 1,
            'name' => 'Test',
            'nested' => $nestedDto,
        ]);

        $array = $dto->toArray();

        expect($array)->toBeArray();
        expect($array['nested'])->toBeArray();
        expect($array['nested']['id'])->toBe(2);
        expect($array['nested']['name'])->toBe('Nested');
    });

    it('handles deeply nested arrays', function () {
        $dto = TestDTO::make([
            'id' => 1,
            'name' => 'Test',
            'data' => [
                'level1' => [
                    'level2' => [
                        'value' => 'deep',
                    ],
                ],
            ],
        ]);

        $array = $dto->toArray();
        expect($array['data']['level1']['level2']['value'])->toBe('deep');
    });

    it('validates all required attributes on fromArray', function () {
        $dto = TestDTO::fromArray(['id' => 1, 'name' => 'Test', 'extra' => 'value']);

        expect($dto)->toBeInstanceOf(TestDTO::class);
    });
});
