<?php

namespace App\Http\Controllers\Admins\Product;

use App\Core\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Product\StoreProductRequest;
use App\Http\Requests\Admins\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductsIndexResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Scoping\Scopes\CategoryScope;
use App\Scoping\Scopes\NameScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('variations.stock', 'variations.productVariationImages', 'categories')->orderBy('products.id', 'desc')->withScopes($this->scopes())->paginate($request->per_page ?? 10);
        return ProductsIndexResource::collection($products);
    }

    public function show(Product $product)
    {
        $product->load(['variations.type', 'variations.stock', 'variations.product', 'variations.productVariationImages', 'variations.wholesalePrices', 'categories']);
        return new ProductResource(
            $product
        );
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();
            $product = Product::create($request->only([
                'name_en',
                'name_ar',
                'price',
                'description',
                'on_order',
                'slug'
            ]));

            $this->handelVariations($request->variations, $product->id);

            if ($request->only('categories')) {
                $product->categories()->attach($request->only('categories')['categories']);
            }


            DB::commit();
            $product->load('variations.stock', 'variations.productVariationImages');

            return new ProductsIndexResource($product);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }

    protected function scopes()
    {
        return [
            'category' => new CategoryScope(),
            'search' => new NameScope()
        ];
    }

    protected function handelVariations($variations, $productId)
    {
        foreach ($variations as $variation) {
            $productVariation = ProductVariation::create([
                'name_en' => $variation['name_en'],
                'name_ar' => $variation['name_ar'],
                'price' => $variation['price'],
                'product_variation_type_id' => 1, //To be changed in case of variation feature
                'product_id' => $productId
            ]);

            $productVariation->stocks()->create(['quantity' => $variation['stock_count']]);

            foreach ($variation['images'] as $key => $imageString) {
                $imageName = $this->saveImage($key, $imageString, $productId, $productVariation->id);
                $productVariation->productVariationImages()->create([
                    'image_path' => $imageName
                ]);
            }
        }
    }

    protected function saveImage($order, $image, $productId, $variationId)
    {
        $imageData = base64_decode(preg_replace('/^data:image\/(\w+);base64,/', '', $image));

        $imageInfo = getimagesizefromstring($imageData);
        $mime = explode('/', $imageInfo['mime'])[1];
        $imageName = $order . '.' . $mime;
        $directory = '/public/products/' . $productId . '/' . $variationId;

        \Storage::put($directory . $imageName, ($imageData));

        return $directory . $imageName;
    }

    public function update(Product $product, UpdateProductRequest $request)
    {
        try {
            $product->update($request->only([
                'name_en',
                'name_ar',
                'price',
                'description',
                'on_order',
                'slug'
            ]));

            if ($request->only('categories')) {
                $product->categories()->sync($request->only('categories')['categories']);
            }

            $product->load('variations.stock', 'variations.productVariationImages');

            return new ProductsIndexResource($product);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            $variations = $product->variations;

            foreach ($variations as $variation) {
                $images = $variation->productVariationImages;


                $variation->productVariationImages()->delete();
                $variation->stocks()->delete();

                $variation->delete();
                foreach ($images as $image) {
                    \Storage::delete($image->image_path);
                }
            }

            $product->delete();

            DB::commit();
            return ResponseHelper::renderCustomSuccessResponse([]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }
}
