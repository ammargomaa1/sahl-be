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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        if ($request->parents_only) {
            $categories = Category::whereNull('parent_id')->orderBy('categories.id','desc')->paginate($request->per_page ?? 10);
        } else {
            $categories = Category::with('parent', 'children', 'images')->orderBy('categories.id','desc')->paginate($request->per_page ?? 10);
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
            'is_main_page_menu' => $request->is_main_page_menu,
            'slug' => Str::slug($request->name_ar, '-'),
        ];

        if ($request->parent_id) {
            $creationArray['parent_id'] = $request->parent_id;
        }

        DB::beginTransaction();

        $category = Category::create(
            $creationArray
        );

        if (!$category) {
            throw new \Exception('Category were not created with body ' . json_encode($creationArray));
        }

        $path = $request->file('image')->store('public/categories/' . $category->id);

        CategoryImage::create([
            'category_id' => $category->id,
            'image_path' => '/' . $path
        ]);

        DB::commit();

        return $category;
    }

    public function update(Category $category, UpdateCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $updateArray = [
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'is_main_page_menu' => $request->is_main_page_menu,
                'slug' => Str::slug($request->name_ar, '-')
            ];

            if ($request->parent_id) {
                $updateArray['parent_id'] = $request->parent_id;
            }

            $category->update($updateArray);

            if ($request->hasFile('image')) {
                // Delete the old image if exists
                $oldImages = $category->images;
                foreach ($oldImages as $oldImage) {
                    Storage::delete(str_replace('/public/', 'public/', $oldImage->image_path));
                    $oldImage->delete();
                }

                // Store the new image
                $path = $request->file('image')->store('public/categories/' . $category->id);

                CategoryImage::create([
                    'category_id' => $category->id,
                    'image_path' => '/' . $path
                ]);
            }

            DB::commit();

            return new CategoryResource($category->refresh());
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }

    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            // Delete associated images if they exist

            // Delete the old image if exists
            $oldImages = $category->images;
            foreach ($oldImages as $oldImage) {
                Storage::delete(str_replace('/public/', 'public/', $oldImage->image_path));
                $oldImage->delete();
            }


            // Detach products associated with the category
            $category->products()->detach();

            // Recursively delete child categories
            $children = $category->children;
            foreach ($children as $child) {
                $this->destroy($child);
            }

            // Delete the category
            $category->delete();

            DB::commit();

            return ResponseHelper::renderCustomSuccessResponse([]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ResponseHelper::render500Response($ex);
        }
    }
}
