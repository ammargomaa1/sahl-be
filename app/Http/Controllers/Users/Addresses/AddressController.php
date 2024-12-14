<?php

namespace App\Http\Controllers\Users\Addresses;

use App\Core\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\Address\AddressStoreRequest;
use App\Http\Requests\Users\Address\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:users']);
    }

    public function index(Request $request)
    {
        return AddressResource::collection(
            $request->user()->addresses
        );
    }

    public function store(AddressStoreRequest $request)
    {
        $address = Address::make($request->only([
            'name',
            'address_1',
            'postal_code',
            'city_id',
            'default'
        ]));

        $request->user()->addresses()->save($address);

        return new AddressResource(
            $address
        );
    }

    public function update(Address $address, UpdateAddressRequest $request)
    {
        if ($address->user_id != $request->user()->id) {
            return ResponseHelper::renderCustomErrorResponse([
                'code' => 403,
                'message' => "address doesn't belong to user"
            ]);
        }

        if ($request->default) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update([
                'default' => false
            ]);
        }

        $address->update($request->only([
            'name',
            'address_1',
            'default'
        ]));

        return new AddressResource(
            $address
        );
    }
}
