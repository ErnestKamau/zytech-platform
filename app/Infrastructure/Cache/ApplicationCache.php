<?php

namespace App\Infrastructure\Cache;

use App\Core\Contracts\CacheStore;
use Illuminate\Support\Facades\Cache;

final class ApplicationCache implements CacheStore
{
    public function __construct(
        private readonly string $prefix = 'zytech',
    ) {}

    public function key(string $name): string
    {
        return "{$this->prefix}.{$name}";
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Cache::get($this->key($key), $default);
    }

    public function put(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool
    {
        return Cache::put($this->key($key), $value, $ttl);
    }

    public function forever(string $key, mixed $value): bool
    {
        return Cache::forever($this->key($key), $value);
    }

    public function forget(string $key): bool
    {
        return Cache::forget($this->key($key));
    }

    public function remember(string $key, int|\DateInterval|null $ttl, callable $callback): mixed
    {
        return Cache::remember($this->key($key), $ttl, $callback);
    }
}
