<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class InformUserWithOrderDetails
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreated $event): void
    {
        // Extract order details and user information from the event
        $order = $event->order;
        $user = $order->user;

        // Prepare email data
        $data = [
            'name' => $user->name,
            'order_id' => $order->id,
            'order_date' => $order->created_at->format('F d, Y'),
            'order_items' => $order->products, // Assuming $order->items gives an array of items
            'total_price' => $order->total(),
        ];

        // Send email to the user
        Mail::send('emails.order-details', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Your Order Details - TheFurnHub');
        });
    }
}
