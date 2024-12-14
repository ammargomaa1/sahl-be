<?php

namespace App\Http\Requests\Users\Orders;

use App\Models\Address;
use App\Rules\ValidShippingMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LVR\CreditCard\CardNumber;

class OrderStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        
        return [
            'credit_card_number' => ['required', new CardNumber],
            'credit_card_name' => 'required|string|max:255',
            'month' => 'required|numeric|between:1,12',
            'year' => 'required|numeric|digits:2',
            'cvc' => 'required|numeric|digits_between:3,4',
            'address_id' =>[
                'required',
                Rule::exists('addresses','id')->where(function($builder) {
                    $builder->where('user_id',$this->user()->id);
                })

            ],
        ];
    }
}
