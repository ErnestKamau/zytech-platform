<?php

namespace App\Core\Services;

use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    /**
     * Run the callback inside a database transaction.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    protected function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
