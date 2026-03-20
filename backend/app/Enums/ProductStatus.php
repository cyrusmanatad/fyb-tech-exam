<?php

enum ProductStatus: string
{
    case PUBLISHED = 'published';
    case OUT_STOCK = 'out-of-stock';
    case DRAFT     = 'draft';
    case INACTIVE  = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::PUBLISHED => 'Published',
            self::OUT_STOCK => 'Out Stock',
            self::DRAFT     => 'Draft List',
            self::INACTIVE  => 'Inactive',
        };
    }

    // Helper to check if publishable
    public function isPublishable(): bool
    {
        return match($this) {
            self::DRAFT,
            self::INACTIVE  => true,
            default         => false,
        };
    }
}