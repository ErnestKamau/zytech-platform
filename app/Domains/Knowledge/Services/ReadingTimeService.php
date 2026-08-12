<?php

namespace App\Domains\Knowledge\Services;

use App\Core\Services\BaseService;

final class ReadingTimeService extends BaseService
{
    public function fromText(string $text, int $wordsPerMinute = 200): int
    {
        $words = str_word_count(strip_tags($text));

        return max(1, (int) ceil($words / $wordsPerMinute));
    }

    /**
     * @param  iterable<string>  $chunks
     */
    public function fromChunks(iterable $chunks, int $wordsPerMinute = 200): int
    {
        $text = collect($chunks)->filter()->implode(' ');

        return $this->fromText($text, $wordsPerMinute);
    }
}
