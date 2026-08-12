<?php

namespace App\Domains\Communication\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Communication\Support\CommunicationCache;
use App\Models\NotificationTemplate;
use Illuminate\Support\Collection;

final class TemplateService extends BaseService
{
    public function __construct(private readonly CacheStore $cache) {}

    public function findByKey(string $key): ?NotificationTemplate
    {
        return $this->all()->first(
            fn (NotificationTemplate $template): bool => $template->key === $key && $template->is_active,
        );
    }

    /**
     * @return Collection<int, NotificationTemplate>
     */
    public function all(): Collection
    {
        /** @var Collection<int, NotificationTemplate>|null $cached */
        $cached = $this->cache->get(CommunicationCache::TEMPLATES);

        if ($cached instanceof Collection) {
            return $cached;
        }

        $templates = NotificationTemplate::query()->orderBy('name')->get();
        $this->cache->put(CommunicationCache::TEMPLATES, $templates, now()->addHour());

        return $templates;
    }

    /**
     * @param  array<string, string>  $replacements
     */
    public function render(string $template, array $replacements): string
    {
        $rendered = $template;

        foreach ($replacements as $key => $value) {
            $rendered = str_replace('{{'.$key.'}}', $value, $rendered);
        }

        return $rendered;
    }

    public function forget(): void
    {
        $this->cache->forget(CommunicationCache::TEMPLATES);
    }
}
