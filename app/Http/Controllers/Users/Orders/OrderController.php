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

            event(new OrderCreated($order));
            $payment = $this->Pay($request, $order);
            $order->payment_id = $payment['payment_id'];

            $order->save();

            DB::commit();

            $order->redirect_url = $payment['redirect_url'];
            return new OrderResource($order);
        } catch (BusinessException $ex) {
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

    protected function Pay(Request $request, Order $order)
    {
        $paymentMethod = new MoyasarPayment;
        $paymentMethod->setOrder($order)
            ->setCreditCardNumber($request->credit_card_number)
            ->setCreditCardName($request->credit_card_name)
            ->setCvC($request->cvc)
            ->setMonth($request->month)
            ->setYear($request->year);

        $result = $paymentMethod->pay();
        info($result);
        if ($result['status'] === 400) {
            throw new BusinessException(400, 'payment failed, ' . implode(' ,', $result['body']['errors']['company'] ? $result['body']['errors']['company'] : $result['body']['errors']));
        } elseif (!in_array($result['status'], [200, 201])) {
            throw new BusinessException($result['status'], 'payment failed');
        }

        return ['payment_id' => $result['body']['id'], 'redirect_url' => $result['body']['source']['transaction_url']];
    }
}
