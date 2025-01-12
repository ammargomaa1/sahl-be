<?php

namespace App\Payment;

use App\Models\Order;
use App\Payment\Exceptions\NoClientSecretException;
use App\Payment\Exceptions\NoOrderIdException;
use App\Payment\Exceptions\NoTokenReturnedException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class PaymobService
{
    protected $apiKey;
    private $authToken;
    private $orderId;

    public function __construct(protected Order $order)
    {
        $this->apiKey = env('PAYMOB_API_KEY');
    }

    // Authenticate with Paymob and get token
    public function createIntention()
    {
        $user = $this->order->user;
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . env('PAYMOB_SECRET'),
        ])->post(env('PAYMOB_BASE_URL') . '/v1/intention/', [
            "amount"=>  $this->order->total()->amount(),
            "items" =>[],
            "currency"=>"EGP",
            "payment_methods" => [(int)config("payment.paymob.payment_methods.card"), (int)config("payment.paymob.payment_methods.wallet")],
            "billing_data" => [
                "first_name"=> $user->name,  // First Name, Last Name, Phone number, & Email are mandatory fields within sending the intention request
                "last_name"=> $user->name,
                "phone_number"=> $user->phone_number,
                "email" => $user->email,
            ],
            "extras" => [
                "order_id" => $this->order->id,
            ],
            "special_reference" => $this->order->id. "-" . Carbon::now()->toDateTimeString(),
            "notification_url" =>  env('API_URL') . "/paymob-webhook", 
            "redirection_url" => env("APP_URL")."/account/orders/".$this->order->id,
        ]);
        
        $data = $response->json();

        if (empty($data["client_secret"]) || empty($data["intention_order_id"])) {
            throw new NoClientSecretException();
        }
        $redirectionUrl = env("PAYMOB_REDIRECT_URL") . '?publicKey=' . env('PAYMOB_PUBLIC_KEY') . '&clientSecret=' . $data['client_secret'];

        return ['redirect_url' => $redirectionUrl, 'payment_order_id' => $data["intention_order_id"]];
    }
}
