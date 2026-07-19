<?php

namespace App\Infrastructure\Queue;

final class QueueName
{
    public const DEFAULT = 'default';

    public const MAIL = 'mail';

    public const MEDIA = 'media';

    public const SEARCH = 'search';

    public const BROADCAST = 'broadcast';

    public const NOTIFICATIONS = 'notifications';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::DEFAULT,
            self::MAIL,
            self::MEDIA,
            self::SEARCH,
            self::BROADCAST,
            self::NOTIFICATIONS,
        ];
    }
}
