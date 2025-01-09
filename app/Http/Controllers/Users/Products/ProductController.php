<?php

namespace App\Http\Controllers\Users\Products;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsIndexResource;
use App\Models\Product;
use App\Scoping\Scopes\CategoryScope;
use App\Scoping\Scopes\NameScope;
use App\Scoping\Scopes\PriceMaxScope;
use App\Scoping\Scopes\PriceMinScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('variations.stock', 'variations.productVariationImages')->withScopes($this->scopes())->paginate($request->per_page ?? 10);
        return ProductsIndexResource::collection($products);
    }

    public function show(Product $product)
    {
        $loadedResources = ['variations.type', 'variations.stock', 'variations.product', 'variations.productVariationImages'];
        if (request()->user() && request()->user()->is_business) {
            $loadedResources[] = 'variations.wholesalePrices';
        }
        $product->load($loadedResources);
        return new ProductResource(
            $product
        );
    }

    protected function scopes()
    {
        return [
            'category' => new CategoryScope(),
            'search' => new NameScope(),
            'min_price' => new PriceMinScope(),
            'max_price' => new PriceMaxScope(),
        ];
    }
}
