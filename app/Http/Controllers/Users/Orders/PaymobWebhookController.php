<?php

namespace App\Http\Controllers\Users\Orders;

use App\Enums\Orders\AOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Middleware\VerifyWebhookHmac;
use App\Models\Order;
use Illuminate\Http\Request;

class PaymobWebhookController extends Controller
{
    public function __construct()
    {
        $this->middleware(VerifyWebhookHmac::class);
    }

    public function action(Request $request): void
    {
        $type = $request->get('type');
        $obj = $request->get('obj');
        if (($type == "TRANSACTION")) {
            $orderId = $obj['order']['id'];

            $order = Order::where('payment_id', $orderId)->first();

            if (($obj['success'] == true) && ($obj['pending'] == false) && ($obj['is_refunded'] == false)) {
                \Log::info("Transaction Process Success");
                $order->status_id = AOrderStatus::PROCESSING;
            } else if (($obj['success'] == false) && ($obj['pending'] == false) && ($obj['is_refunded'] == false)) {
                \Log::info('Transaction failed');
                $order->status_id = AOrderStatus::PAYMENT_FAILED;
            }

            $order->save();

        }
    }
}
