<?php

namespace App\DTOs;

use App\DTOs\OrderItemData;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Collection;

class OrderData
{
    public function __construct(
        public readonly int        $user_id,
        public readonly string     $currency,
        public readonly float      $discount,
        public readonly float      $tax,
        public readonly float      $shipping_fee,
        public readonly ?string    $payment_method,
        public readonly ?string    $shipping_method,
        public readonly ?string    $notes,
        public readonly Collection $items, // Collection<OrderItemData>
    ) {}

    public static function fromRequest($request, int $userId): self
    {
        return new self(
            user_id:         $userId,
            currency:        $request['currency']        ?? 'PHP',
            discount:        $request['discount']        ?? 0,
            tax:             $request['tax']             ?? 0,
            shipping_fee:    $request['shipping_fee']    ?? 0,
            payment_method:  $request['payment_method'],
            shipping_method: $request['shipping_method'],
            notes:           $request['notes'],
            items:           collect($request['items'])
                                ->map(fn($item) => OrderItemData::fromArray($item)),
        );
    }
}