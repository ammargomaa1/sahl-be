<?php

namespace App\Http\Controllers\Admins\Order;

use App\Core\Helpers\ResponseHelper;
use App\Enums\Orders\AOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Payment\MoyasarPayment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with([
            'address',
            'user'
        ])
            ->without([
                'products',
                'products.stock',
                'products.type',
                'products.product',
                'products.product.variations',
                'products.product.variations.stock',
            ])
            ->orderBy('orders.id', 'desc')
            ->paginate($request->per_page ?? 10);

        return OrderResource::collection($orders);
    }

    public function show(Order $order)
    {
        $order->load([
            'products',
            'products.stock',
            'products.type',
            'products.product',
            'products.product.variations',
            'products.product.variations.stock',
            'address',
            'user'
        ])
            ->get();
        return (new OrderResource($order));
    }

    public function update(Order $order, UpdateOrderRequest $request)
    {
        $this->validateOrderPayment($order);
        $order->fresh();
        if (in_array($order->status_id, [(AOrderStatus::PENDING)->value, (AOrderStatus::PAYMENT_FAILED)->value])) {
            return ResponseHelper::renderCustomErrorResponse([
                'message' => _('ALL.ORDERS.ORDER_MUST_BE_PAID'),
                'code' => 403
            ]);
        }
        if ($request->status_id == (AOrderStatus::PAYMENT_FAILED)->value) {
            return ResponseHelper::renderCustomErrorResponse([
                'message' => _('ALL.ORDERS.CANNOT_UPDATE_FAILED_PAYMENT_STATUS'),
                'code' => 403
            ]);
        }

        $order->update([
            'status_id' => $request->status_id
        ]);

        $order->load([
            'products' => function ($query) {
                $query->withPivot('quantity');
            },
            'products.stock',
            'products.type',
            'products.product',
            'products.product.variations',
            'products.product.variations.stock',
            'address',
        ])->get();

        return (new OrderResource($order));
    }

    private function validateOrderPayment(Order $order)
    {
        try {
            if ($order->status_id == AOrderStatus::PENDING) {
                $payment = MoyasarPayment::getPayment($order->payment_id);

                switch ($payment->status) {
                    case 'paid':
                        $order->status_id = AOrderStatus::PROCESSING;
                        $order->save();
                        break;

                    case 'failed':
                        $order->status_id = AOrderStatus::PAYMENT_FAILED;
                        $order->save();
                        break;

                    default:

                        break;
                }
            }

        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }
}
