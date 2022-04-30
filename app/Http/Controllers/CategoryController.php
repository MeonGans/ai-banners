<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreRequest;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Banner;
use App\Models\Category;
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//use Jenssegers\Date\Date;

class CategoryController extends Controller
{
    protected Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return CategoryCollection
     */
    public function index(): CategoryCollection
    {
        $category = $this->category->with(['group', 'user']);
        return new CategoryCollection($category->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return CategoryResource
     */
    public function store(StoreRequest $request): CategoryResource
    {
        $category = $this->category->create($request->validated());
        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show($category_id, $option = null): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        //random, new, premium, popular
        $banners = Banner::query()->where('category_id', $category_id);

        //Отображение баннеров выбраной категории + информация о самой категории
        switch ($option) {
            case 'random':
                $banners->inRandomOrder();
                break;
            case 'premium':
                $banners->where('is_premium', true)->orderByDesc('created_at');
                break;
            case 'popular':
                $banners->orderByDesc('used');
                break;
            default:
                $banners->orderByDesc('created_at');
        }

        //ДОБАВИТЬ ПРОВЕРКУ НА ОТОБРАЖЕНИЕ ИНДИВИДУАЛЬНЫХ БАННЕРОВ
        return BannerResource::collection($banners->get());


        //Проверка отображения, нельзя выводить пользователю без премиума
        // премиальную категорию, возврат ошибки "нет доступа"
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Models\Category $category
     * @param \App\Http\Requests\Category\StoreRequest $request
     * @return CategoryResource
     */
    public function update(Category $category, StoreRequest $request): CategoryResource
    {
        $category->update($request->validated());
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return void
     */
    public function destroy(Category $category): void
    {
        $category->delete();
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param $id
     * @return \App\Http\Resources\CategoryResource
     */

    public function restore($id): CategoryResource
    {
        $category = Category::withTrashed()->find($id)->restore();
        return new CategoryResource($category);
    }
}
