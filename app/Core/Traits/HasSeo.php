<?php

namespace App\Core\Traits;

trait HasSeo
{
    public function initializeHasSeo(): void
    {
        $this->mergeCasts([
            'seo' => 'array',
        ]);
    }

    public function seoTitle(): ?string
    {
        return $this->seo['title'] ?? null;
    }

    public function seoDescription(): ?string
    {
        return $this->seo['description'] ?? null;
    }

    /**
     * @param  array{title?: string, description?: string, keywords?: array<int, string>}  $seo
     */
    public function setSeo(array $seo): void
    {
        $this->seo = array_merge($this->seo ?? [], $seo);
    }
}
