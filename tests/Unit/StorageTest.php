<?php

use Illuminate\Support\Facades\Cache;
use sakoora0x\Telegram\Storage;

describe('Storage', function () {
    beforeEach(function () {
        Cache::flush();
        $this->storage = new Storage('test_key');
    });

    it('can be instantiated with a key', function () {
        expect($this->storage)->toBeInstanceOf(Storage::class);
    });

    it('can store and retrieve simple data', function () {
        $this->storage->storeData('name', 'John');

        expect($this->storage->retrieveData('name'))->toBe('John');
    });

    it('returns default value when key does not exist', function () {
        expect($this->storage->retrieveData('missing', 'default'))->toBe('default');
    });

    it('returns null for missing key without default', function () {
        expect($this->storage->retrieveData('missing'))->toBeNull();
    });

    it('can store and retrieve nested data using dot notation', function () {
        $this->storage->storeData('user.name', 'John');
        $this->storage->storeData('user.age', 30);

        expect($this->storage->retrieveData('user.name'))->toBe('John');
        expect($this->storage->retrieveData('user.age'))->toBe(30);
    });

    it('can retrieve parent of nested data', function () {
        $this->storage->storeData('user.name', 'John');
        $this->storage->storeData('user.age', 30);

        $user = $this->storage->retrieveData('user');

        expect($user)->toBeArray();
        expect($user['name'])->toBe('John');
        expect($user['age'])->toBe(30);
    });

    it('can store deeply nested data', function () {
        $this->storage->storeData('user.profile.address.city', 'New York');

        expect($this->storage->retrieveData('user.profile.address.city'))->toBe('New York');
    });

    it('can retrieve deeply nested data', function () {
        $this->storage->storeData('user.profile.address.city', 'New York');
        $this->storage->storeData('user.profile.address.country', 'USA');

        $address = $this->storage->retrieveData('user.profile.address');

        expect($address)->toBeArray();
        expect($address['city'])->toBe('New York');
        expect($address['country'])->toBe('USA');
    });

    it('returns default for missing nested keys', function () {
        $this->storage->storeData('user.name', 'John');

        expect($this->storage->retrieveData('user.age', 25))->toBe(25);
    });

    it('can forget simple keys', function () {
        $this->storage->storeData('name', 'John');
        $result = $this->storage->forget('name');

        // After forgetting a nested key, Storage implementation stores empty array for main key
        expect($result)->toBeInstanceOf(Storage::class);
        $retrieved = $this->storage->retrieveData('name');
        // Can be null or empty array depending on implementation
        expect($retrieved === null || $retrieved === [])->toBeTrue();
    });

    it('can forget nested keys', function () {
        $this->storage->storeData('user.name', 'John');
        $this->storage->storeData('user.age', 30);

        $this->storage->forget('user.age');

        expect($this->storage->retrieveData('user.age'))->toBeNull();
        expect($this->storage->retrieveData('user.name'))->toBe('John');
    });

    it('forget returns the storage instance for chaining', function () {
        $result = $this->storage->forget('key');

        expect($result)->toBeInstanceOf(Storage::class);
    });

    it('can store arrays', function () {
        $data = ['id' => 1, 'name' => 'Test', 'items' => [1, 2, 3]];
        $this->storage->storeData('data', $data);

        expect($this->storage->retrieveData('data'))->toBe($data);
    });

    it('can overwrite existing data', function () {
        $this->storage->storeData('name', 'John');
        $this->storage->storeData('name', 'Jane');

        expect($this->storage->retrieveData('name'))->toBe('Jane');
    });

    it('can overwrite nested data', function () {
        $this->storage->storeData('user.name', 'John');
        $this->storage->storeData('user.name', 'Jane');

        expect($this->storage->retrieveData('user.name'))->toBe('Jane');
    });

    it('handles multiple storage instances with different keys', function () {
        $storage1 = new Storage('key1');
        $storage2 = new Storage('key2');

        $storage1->storeData('name', 'Storage 1');
        $storage2->storeData('name', 'Storage 2');

        expect($storage1->retrieveData('name'))->toBe('Storage 1');
        expect($storage2->retrieveData('name'))->toBe('Storage 2');
    });

    it('can store and retrieve boolean values', function () {
        $this->storage->storeData('active', true);
        $this->storage->storeData('deleted', false);

        expect($this->storage->retrieveData('active'))->toBeTrue();
        expect($this->storage->retrieveData('deleted'))->toBeFalse();
    });

    it('can store and retrieve numeric values', function () {
        $this->storage->storeData('count', 42);
        $this->storage->storeData('price', 19.99);

        expect($this->storage->retrieveData('count'))->toBe(42);
        expect($this->storage->retrieveData('price'))->toBe(19.99);
    });

    it('preserves data types on retrieval', function () {
        $this->storage->storeData('string', 'test');
        $this->storage->storeData('int', 123);
        $this->storage->storeData('float', 12.34);
        $this->storage->storeData('bool', true);
        $this->storage->storeData('array', [1, 2, 3]);

        expect($this->storage->retrieveData('string'))->toBeString();
        expect($this->storage->retrieveData('int'))->toBeInt();
        expect($this->storage->retrieveData('float'))->toBeFloat();
        expect($this->storage->retrieveData('bool'))->toBeBool();
        expect($this->storage->retrieveData('array'))->toBeArray();
    });
});
