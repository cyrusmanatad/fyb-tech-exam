<?php

namespace App\DTOs;

class ProductData
{
    public function __construct(
        public readonly ?int $category_id,
        public readonly string $desc,
        public readonly ?string $desc_long,
        public readonly string $uom,
        public readonly float $price,
        public readonly float $sell_price,
        public readonly string $status,
        public readonly int $stock,
        public readonly ?string $slug,
        public readonly ?string $currency,
        public readonly string $sku,
        public readonly int $user_id,
    ) {}

    public static function fromRequest(array $data, int $userId): self
    {
        return new self(
            $data['category_id'] ?? null,
            $data['desc'],
            $data['desc_long'] ?? null,
            $data['uom'],
            $data['price'],
            $data['sell_price'],
            $data['status'],
            $data['stock'],
            $data['slug'] ?? null,
            $data['currency'] ?? null,
            $data['sku'],
            $userId
        );
    }
}