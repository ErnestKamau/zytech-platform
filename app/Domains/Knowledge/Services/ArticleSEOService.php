<?php

namespace App\Domains\Knowledge\Services;

use App\Core\Services\BaseService;
use App\Domains\Knowledge\Data\ArticleData;
use App\Models\Article;
use Illuminate\Support\Str;

final class ArticleSEOService extends BaseService
{
    public function ensure(Article $article): Article
    {
        $title = filled($article->meta_title)
            ? $article->meta_title
            : $this->titleFor($article);

        $description = filled($article->meta_description)
            ? $article->meta_description
            : $this->descriptionFor($article);

        $ogImage = filled($article->og_image_key)
            ? $article->og_image_key
            : $article->image_key;

        if (
            $article->meta_title === $title
            && $article->meta_description === $description
            && $article->og_image_key === $ogImage
        ) {
            return $article;
        }

        $article->forceFill([
            'meta_title' => $title,
            'meta_description' => $description,
            'og_image_key' => $ogImage,
        ])->save();

        return $article->refresh();
    }

    /**
     * @return array{title: string, description: string, og_image_key: ?string}
     */
    public function forPage(ArticleData $article): array
    {
        return [
            'title' => $article->metaTitle !== ''
                ? $article->metaTitle
                : $article->title.' — Zytech Contractors',
            'description' => $article->metaDescription !== ''
                ? $article->metaDescription
                : Str::limit($article->excerpt, 160),
            'og_image_key' => $article->ogImageKey ?? $article->imageKey,
        ];
    }

    private function titleFor(Article $article): string
    {
        return $article->title.' — Zytech Contractors';
    }

    private function descriptionFor(Article $article): string
    {
        return Str::limit(trim(strip_tags($article->excerpt ?? '')), 160);
    }
}
