<?php

namespace App\Http\Controllers\Users\Orders;

use App\Enums\Orders\AOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class MoyasarWebhookController extends Controller
{
    public function action(Request $request){
        if (!$this->validateMoyasar($request->secret_token)) {
            return;
        }

        $order = Order::where('payment_id', $request->data['id'])->first();
        info($order);
        if ($order) {
            $request->type == 'payment_paid' ? $order->status_id = AOrderStatus::PROCESSING : $order->status_id = AOrderStatus::PAYMENT_FAILED;

            $order->save();
        }
    }

    private function validateMoyasar($token) {
        if ($token == '6O5NwmbBAlpkUwecW5vtYUAEUL7H0kG0RDWNnAMKbtktxBmJ4UdTVe3cxp81CR7029YzqkRpWgyuPget6vK8oc5l91TWRkkdQGExBCb1hkW7Z79kc9aGjqheMkeU1JCsZ8kxO1vVnfbBpekmnexYGFtohVzGV59YuwnOx0SgfQb6LRcFjNffrJcFDZjlpJxp9cmdPFt448xyxXksz9NXgT19uMLzVY48gstjqkdhOiNKJFXaZjzqCL0J74QtL1CA') {
            return true;
        }
        
        return false;
    }
}
