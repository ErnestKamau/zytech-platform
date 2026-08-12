<?php

namespace App\Core\Enums;

enum SettingType: string
{
    case String = 'string';
    case Text = 'text';
    case Boolean = 'boolean';
    case Integer = 'integer';
    case Json = 'json';
    case Url = 'url';
    case Email = 'email';

    public function label(): string
    {
        return match ($this) {
            self::String => 'String',
            self::Text => 'Text',
            self::Boolean => 'Boolean',
            self::Integer => 'Integer',
            self::Json => 'JSON',
            self::Url => 'URL',
            self::Email => 'Email',
        };
    }
}
