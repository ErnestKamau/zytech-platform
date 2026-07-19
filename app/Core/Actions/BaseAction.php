<?php

namespace App\Core\Actions;

abstract class BaseAction
{
    /**
     * Execute the action.
     *
     * Concrete actions should implement __invoke() or a clearly named execute() method.
     */
    abstract public function handle(mixed ...$arguments): mixed;
}
