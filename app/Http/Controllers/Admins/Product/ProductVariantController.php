<?php

namespace App\Http\Controllers\Admins\Product;

use App\Core\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Product\CreateProductVariantRequest;
use App\Http\Requests\Admins\Product\CreateProductVariantWholeSalePrice;
use App\Http\Requests\Admins\Product\UpdateProductVariantRequest;
use App\Http\Requests\Admins\Product\UpdateProductVariantWholeSalePrice;
use App\Http\Requests\Admins\Product\UpdateVariantQuantity;
use App\Http\Resources\ProductVariationsResource;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\ProductVariationWholesalePrice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    public function show(ProductVariation $productVariant)
    {
        $productVariant->load('stock', 'productVariationImages', 'wholesalePrices');
        return (new ProductVariationsResource($productVariant));
    }

    public function update(ProductVariation $productVariant, UpdateProductVariantRequest $request)
    {
        try {
            DB::beginTransaction();

            // Update basic fields
            $productVariant->update($request->only([
                'name_ar',
                'name_en',
                'price',
            ]));
            
            $oldImagesCollectedIds = [];

            if ($request->old_images) {
                $oldImagesCollectedIds = collect($request->old_images)->pluck('id')->toArray();

            }
            
            // Delete existing images (optional: you can also delete files from storage if needed)
            foreach ($productVariant->productVariationImages as $image) {
                if (in_array($image->id, $oldImagesCollectedIds)) {
                    continue;
                }

                \Storage::delete($image->image_path); // Delete the file from storage
                $image->delete();                     // Delete the image record from DB
            }

            // Save new images
            if ($request->images) {
                foreach ($request->images as $key => $imageString) {
                    $imageName = $this->saveImage(
                        $key,
                        $imageString,
                        $productVariant->product_id,
                        $productVariant->id
                    );

                    $productVariant->productVariationImages()->create([
                        'image_path' => $imageName,
                    ]);
                }
            }


            DB::commit();
            return new ProductVariationsResource($productVariant->fresh('productVariationImages'));
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }


    public function create($productId, CreateProductVariantRequest $request)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return ResponseHelper::render404Response();
            }
            DB::beginTransaction();

            $variation = $product->variations()->create($request->only([
                'name_ar',
                'name_en',
                'price',
            ]) + [
                'product_variation_type_id' => 1, //To be changed in case of variation feature
            ]);
            $variation->stocks()->create([
                'quantity' => $request->stock_count
            ]);

            foreach ($request['images'] as $key => $imageString) {
                $imageName = $this->saveImage($key, $imageString, $productId, $variation->id);
                $variation->productVariationImages()->create([
                    'image_path' => $imageName
                ]);
            }

            DB::commit();
            return (new ProductVariationsResource($variation));
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }

    public function destroy(ProductVariation $productVariant)
    {
        try {
            DB::beginTransaction();
            $images = $productVariant->productVariationImages;


            $productVariant->productVariationImages()->delete();
            $productVariant->stocks()->delete();

            $productVariant->delete();
            foreach ($images as $image) {
                \Storage::delete($image->image_path);
            }

            DB::commit();
            return ResponseHelper::renderCustomSuccessResponse([]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }

    public function addWholeSalePricesToProductVariant(ProductVariation $productVariant, CreateProductVariantWholeSalePrice $request)
    {
        try {
            $prices = [];
            foreach ($request->only('prices')['prices'] as $price) {
                $prices[] = $price + [
                    'product_variation_id' => $productVariant->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            ProductVariationWholesalePrice::insert($prices);
            return (new ProductVariationsResource($productVariant->load('wholesalePrices')));
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }

    public function updateWholeSalePricesToProductVariant(ProductVariation $productVariant, UpdateProductVariantWholeSalePrice $request)
    {
        try {
            $productVariant->wholesalePrices()->delete();
            $prices = [];
            foreach ($request->only('prices')['prices'] as $price) {
                $prices[] = $price + [
                    'product_variation_id' => $productVariant->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            ProductVariationWholesalePrice::insert($prices);
            return (new ProductVariationsResource($productVariant->load('wholesalePrices')));
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }

    public function updateVariantStock(ProductVariation $productVariant, UpdateVariantQuantity $request)
    {
        try {

            $productVariant->stocks()->create([
                'quantity' => $request->stock_count
            ]);

            return (new ProductVariationsResource($productVariant));
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }

    protected function saveImage($order, $image, $productId, $variationId)
    {
        $imageData = base64_decode(preg_replace('/^data:image\/(\w+);base64,/', '', $image));

        $imageInfo = getimagesizefromstring($imageData);
        $mime = explode('/', $imageInfo['mime'])[1];
        $imageName = $order . '.' . $mime;
        $directory = '/public/products/' . $productId . '/' . $variationId . '/';
        \Storage::put($directory . $imageName, ($imageData));

        return $directory . $imageName;
    }
}
