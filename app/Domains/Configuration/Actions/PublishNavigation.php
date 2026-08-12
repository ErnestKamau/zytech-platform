<?php

namespace App\Domains\Configuration\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Configuration\Services\NavigationService;
use App\Models\NavigationMenu;

final class PublishNavigation extends BaseAction
{
    public function __construct(
        private readonly NavigationService $navigation,
    ) {}

    public function handle(mixed ...$arguments): NavigationMenu
    {
        /** @var NavigationMenu $menu */
        $menu = $arguments[0];

        return $this->navigation->publish($menu);
    }
}
