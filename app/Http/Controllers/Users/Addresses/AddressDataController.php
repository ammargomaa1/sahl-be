<?php

namespace App\Http\Controllers\Users\Addresses;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\ProvinceResource;
use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;

class AddressDataController extends Controller
{
    public function cities(Request $request){
        if ($request->province_id) {
            $cities = City::orderby('name_en','asc')->where('province_id', $request->province_id)->get();
        }else {
            $cities = City::orderby('name_en','asc')->get();
        }
        return CityResource::collection($cities);
    }

    public function provinces(){
        return ProvinceResource::collection(Province::orderby('name_en','asc')->get());
    }
}
