<?php

namespace App\Core\Contracts;

interface CacheStore
{
    public function get(string $key, mixed $default = null): mixed;

    public function put(string $key, mixed $value, int|\DateInterval|null $ttl = null): bool;

    public function forever(string $key, mixed $value): bool;

    public function forget(string $key): bool;

    public function remember(string $key, int|\DateInterval|null $ttl, callable $callback): mixed;
}
