<?php

namespace App\Filament\Widgets;

use App\Domains\Operations\Services\ActivityLogger;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\QuotationRequests\QuotationRequestResource;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\DomainActivity;
use App\Models\FollowUp;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class RecentActivityWidget extends Widget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.recent-activity';

    public static function canView(): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return $user->can('clients.view')
            || $user->can('commerce.view')
            || $user->can('commerce.manage')
            || $user->can('quotations.view')
            || $user->can('quotations.manage');
    }

    /**
     * @return list<array{label: string, actor: string, when: string, url: string|null}>
     */
    public function getItems(): array
    {
        return app(ActivityLogger::class)
            ->recent(15)
            ->map(fn (DomainActivity $activity): array => [
                'label' => ActivityLogger::labelFor($activity->event),
                'actor' => $activity->actor?->name ?? 'System',
                'when' => $activity->created_at?->diffForHumans() ?? '',
                'url' => $this->urlFor($activity),
            ])
            ->all();
    }

    private function urlFor(DomainActivity $activity): ?string
    {
        $subject = $activity->subject;

        if ($subject === null) {
            return null;
        }

        try {
            return match (true) {
                $subject instanceof QuotationRequest => QuotationRequestResource::getUrl('index'),
                $subject instanceof Quotation => QuotationResource::getUrl('index'),
                $subject instanceof Order => OrderResource::getUrl('index'),
                $subject instanceof FollowUp => null,
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }
}
