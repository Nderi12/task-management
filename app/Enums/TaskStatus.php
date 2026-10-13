<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Completed => 'Completed',
        };
    }

    /**
     * Bootstrap 4 contextual class suffix (badge-*, table-*, etc.).
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Completed => 'success',
        };
    }

    public function toggled(): self
    {
        return match ($this) {
            self::Pending => self::Completed,
            self::Completed => self::Pending,
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
