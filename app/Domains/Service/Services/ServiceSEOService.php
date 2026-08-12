<?php

namespace App\Domains\Service\Services;

use App\Core\Services\BaseService;
use App\Domains\Service\Data\ServiceData;
use App\Models\Service;
use Illuminate\Support\Str;

final class ServiceSEOService extends BaseService
{
    public function ensure(Service $service): Service
    {
        $title = filled($service->meta_title)
            ? $service->meta_title
            : $this->titleFor($service);

        $description = filled($service->meta_description)
            ? $service->meta_description
            : $this->descriptionFor($service);

        $ogImage = filled($service->og_image_key)
            ? $service->og_image_key
            : $service->image_key;

        if (
            $service->meta_title === $title
            && $service->meta_description === $description
            && $service->og_image_key === $ogImage
        ) {
            return $service;
        }

        $service->forceFill([
            'meta_title' => $title,
            'meta_description' => $description,
            'og_image_key' => $ogImage,
        ])->save();

        return $service->refresh();
    }

    /**
     * @return array{title: string, description: string, og_image_key: ?string}
     */
    public function forPage(ServiceData $service): array
    {
        return [
            'title' => $service->metaTitle !== ''
                ? $service->metaTitle
                : $service->title.' — Zytech Contractors',
            'description' => $service->metaDescription !== ''
                ? $service->metaDescription
                : Str::limit($service->excerpt !== '' ? $service->excerpt : $service->body, 160),
            'og_image_key' => $service->ogImageKey ?? $service->imageKey,
        ];
    }

    private function titleFor(Service $service): string
    {
        return $service->title.' — Zytech Contractors';
    }

    private function descriptionFor(Service $service): string
    {
        $source = filled($service->excerpt) ? $service->excerpt : (string) $service->body;

        return Str::limit(trim(strip_tags($source)), 160);
    }
}
