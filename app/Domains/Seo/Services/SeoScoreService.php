<?php

namespace App\Domains\Seo\Services;

use App\Core\Services\BaseService;
use App\Models\SeoMetadata;

final class SeoScoreService extends BaseService
{
    /**
     * Lightweight heuristic score (0–100) used as an AI-SEO foundation.
     *
     * @param  array{title?: ?string, description?: ?string, content?: ?string}  $payload
     */
    public function score(array $payload): int
    {
        $score = 0;
        $title = trim((string) ($payload['title'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));
        $content = trim((string) ($payload['content'] ?? ''));

        if ($title !== '') {
            $score += 20;
            $len = mb_strlen($title);
            if ($len >= 30 && $len <= 60) {
                $score += 15;
            } elseif ($len > 10) {
                $score += 8;
            }
        }

        if ($description !== '') {
            $score += 20;
            $len = mb_strlen($description);
            if ($len >= 120 && $len <= 160) {
                $score += 15;
            } elseif ($len > 50) {
                $score += 8;
            }
        }

        if ($content !== '') {
            $words = str_word_count(strip_tags($content));
            $score += min(30, (int) floor($words / 20));
        }

        return min(100, $score);
    }

    /**
     * @param  array<string, mixed>|null  $structuredData
     */
    public function upsertForPath(
        string $path,
        string $entityType,
        ?string $entityId,
        ?string $title,
        ?string $description,
        ?string $ogImage = null,
        ?array $structuredData = null,
    ): SeoMetadata {
        $score = $this->score([
            'title' => $title,
            'description' => $description,
            'content' => $description,
        ]);

        return SeoMetadata::query()->updateOrCreate(
            ['path' => $path],
            [
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'title' => $title,
                'description' => $description,
                'canonical_url' => url($path),
                'og_image' => $ogImage,
                'structured_data' => $structuredData,
                'seo_score' => $score,
            ],
        );
    }
}
