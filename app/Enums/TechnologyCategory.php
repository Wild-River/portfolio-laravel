<?php

namespace App\Enums;

enum TechnologyCategory: string
{
    case Frontend = 'frontend';
    case Backend = 'backend';
    case Service = 'service';
    case Tool = 'tool';

    /**
     * Get the heading shown above the group of technologies.
     */
    public function label(): string
    {
        return match ($this) {
            self::Frontend => 'Frontend',
            self::Backend => 'Backend / Data',
            self::Service => 'External services',
            self::Tool => 'Hosting / Tools',
        };
    }
}
