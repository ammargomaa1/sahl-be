<?php

namespace App\Http\Controllers\Admins\Category;

use App\Core\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Category\CreateCategory;
use App\Http\Requests\Admins\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\CategoryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        if ($request->parents_only) {
            $categories = Category::whereNull('parent_id')->ordered()->paginate($request->per_page ?? 10);
        } else {
            $categories = Category::with('parent', 'children', 'images')->ordered()->paginate($request->per_page ?? 10);
        }
        return CategoryResource::collection(
            $categories
        );
    }

    public function store(CreateCategory $request)
    {
        try {
            $category = $this->createCategory($request);
            return (new CategoryResource(
                $category
            ));
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }

    private function createCategory(CreateCategory $request)
    {
        $creationArray = [
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'slug' => Str::slug($request->name_ar, '-'),
        ];

        if ($request->parent_id) {
            $creationArray['parent_id'] = $request->parent_id;
        }

        DB::beginTransaction();

        $category = Category::create(
            $creationArray
        );

        $path = $request->file('image')->store('public/categories/' . $category->id);

        CategoryImage::create([
            'category_id' => $category->id,
            'image_path' => '/'.$path
        ]);

        DB::commit();

        return $category;
    }

    public function update(Category $category, UpdateCategoryRequest $request)
    {
        try {
            $category->update($request->only([
                'name_ar',
                'name_en',
                'parent_id',
                'slug'
            ]));

            return (new CategoryResource(
                $category->refresh()
            ));
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }

    public function destroy(Category $category)
    {
        try {

            // $images = $category->images;

            // foreach ($images as $image) {
            //     \Storage::delete($image->image_path);
            // }


            return ResponseHelper::renderCustomSuccessResponse([]);
        } catch (\Exception $ex) {
            return ResponseHelper::render500Response($ex);
        }
    }
}
