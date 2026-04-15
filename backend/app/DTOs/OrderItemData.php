<?php

namespace App\DTOs;

class OrderItemData
{
    public function __construct(
        public readonly int    $variant_id,
        public readonly int    $quantity,
        public readonly string $price_type,
    ) {}

    public static function fromArray(array $item): self
    {
        return new self(
            variant_id: $item['variant_id'],
            quantity:   $item['quantity'],
            price_type: $item['price_type'],
        );
    }
}