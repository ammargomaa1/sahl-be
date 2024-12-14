<?php

namespace App\Payment;

use App\Models\Order;
use App\Payment\contracts\IPayment;
use Illuminate\Support\Facades\Http;
use Moyasar\Moyasar;
use Moyasar\Providers\PaymentService;

class MoyasarPayment implements IPayment
{
    private $payment;
    private $paymentService;
    private $secretKey;
    private string $creditCardName;
    private string $creditCardNumber;
    private string $cvc;
    private string $month;
    private string $year;
    private Order $order;
    const BASE_URL = 'https://api.moyasar.com/v1/payments';
    public function __construct()
    {
        $this->secretKey = env('MOYASAR_SECRET_KEY');
    }
    public function pay()
    {
        $this->payment = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->secretKey)
        ])->post(self::BASE_URL, [
            'amount' => $this->order->total()->amount(),
            'currency' => env('CURRENCY', 'SAR'),
            'description' => 'Payment for order #' . $this->getOrder()->id,
            "callback_url" => env("APP_URL")."/account/orders/".$this->getOrder()->id,
            "source" => [
                "type" => "creditcard",
                "name" => $this->getCreditCardName(),
                "number" => $this->getCreditCardNumber(),
                "cvc" => $this->getCvc(),
                "month" => $this->getMonth(),
                "year" => $this->getYear()
            ],
        ]);

        return ['status' => $this->payment->status(), 'body' => $this->payment->json()];
    }

    public static function getPayment($id){
        Moyasar::setApiKey(env('MOYASAR_SECRET_KEY'));
        $paymentService = new PaymentService;
        return $paymentService->fetch($id);
    }

    public function setOrder(Order $order){
        $this->order = $order;
        return $this;
    }

    public function setCreditCardName(string $creditCardName){
        $this->creditCardName = $creditCardName;
        return $this;
    }

    public function setCreditCardNumber(string $creditCardNumber){
        $this->creditCardNumber = $creditCardNumber;
        return $this;
    }

    private function getOrder(){
        return $this->order;
    }

    private function getCreditCardName(){
        return $this->creditCardName;
    }

    private function getCreditCardNumber(){
        return $this->creditCardNumber;
    }

    

    /**
     * Get the value of cvc
     */
    private function getCvc(): string
    {
        return $this->cvc;
    }

    /**
     * Set the value of csv
     */
    public function setCvc(string $cvc): self
    {
        $this->cvc = $cvc;

        return $this;
    }

    /**
     * Get the value of month
     */
    private function getMonth(): string
    {
        return $this->month;
    }

    /**
     * Set the value of month
     */
    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get the value of year
     */
    private function getYear(): string
    {
        return $this->year;
    }

    /**
     * Set the value of year
     */
    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }
}
