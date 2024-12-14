<?php

namespace App\Enums\Orders;

enum AOrderStatus: int
{
    case PENDING = 1;
    case PROCESSING = 2;
    case ON_DELIVERY = 3;
    case COMPLETED = 4;
    case PAYMENT_FAILED = 5;

    public static function getOrderStatus($statusId)
    {
        return match ($statusId) {
            1 => 'pending',
            2 => 'processing',
            3 => 'on_delivery',
            4 => 'completed',
            5 => 'payment_failed',
            default => 'unknown'
        };
    }
}
