<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCreated;
use App\Models\Admin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyAdminsWithNewOrderDetails
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

        $admins = Admin::all();
        foreach ($admins as $admin) {
            Mail::send('emails.admin-order-details', $data, function ($message) use ($admin) {
                $message->to($admin->email)
                        ->subject('New Order - TheFurnHub');
            });
        }
    }
}
