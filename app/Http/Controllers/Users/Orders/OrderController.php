<?php

namespace App\Http\Controllers\Users\Orders;

use App\Cart\Cart;
use App\Core\Helpers\ResponseHelper;
use App\Enums\Orders\AOrderStatus;
use App\Events\Order\OrderCreated;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Orders\OrderCashStoreRequest;
use App\Http\Requests\Users\Orders\OrderStoreRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Payment\MoyasarPayment;
use App\Payment\PaymobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['cart.sync', 'cart.isnotempty'])->only('store');
    }

    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with([
                'products',
                'products.stock',
                'products.type',
                'products.product',
                'products.product.variations',
                'products.product.variations.stock',
                'address',
            ])
            ->latest()
            ->paginate($request->per_page ?? 10);

        return OrderResource::collection($orders);
    }

    public function store(OrderStoreRequest $request, Cart $cart)
    {
        try {
            DB::beginTransaction();
            $order = $this->createOrder($request, $cart);

            $order->products()->sync($cart->products()->forSyncing());

            $payment = $this->Pay($order);
            $order->payment_id = $payment['payment_order_id'];
            
            $order->save();
            
            DB::commit();
            
            event(new OrderCreated($order));
            $order->redirect_url = $payment['redirect_url'];
            return new OrderResource($order);
        } catch (BusinessException $ex) {
            DB::rollBack();
            return ResponseHelper::renderCustomErrorResponse([
                'message' => $ex->getMessage(),
                'code' => $ex->getStatusCode(),
            ]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }

    public function payWithCash(OrderCashStoreRequest $request,Cart $cart)
    {
        
        $order = $this->createOrder($request,$cart);

        $order->products()->sync($cart->products()->forSyncing());

        event(new OrderCreated($order));

        return new OrderResource($order);

    }

    public function show(Order $order)
    {
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
            'user'
        ])
            ->get();

        return (new OrderResource($order));
    }

    public function validateOrderPayment(Order $order, Request $request)
    {
        try {
            if ($order->status_id != AOrderStatus::PROCESSING) {
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

            $order = $request->user()->orders()
                ->with([
                    'products',
                    'products.stock',
                    'products.type',
                    'products.product',
                    'products.product.variations',
                    'products.product.variations.stock',
                    'address',
                ])
                ->where('orders.id', $order->id)
                ->first();
            return new OrderResource($order);
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }

    protected function createOrder(Request $request, Cart $cart)
    {
        return $request->user()->orders()->create(
            $request->only(['address_id']) + [
                'subtotal' => $cart->subtotal()->amount()
            ]
        );
    }

    protected function Pay(Order $order)
    {
        $paymentMethod = new PaymobService($order);
        return $paymentMethod->createIntention();
    }
}
