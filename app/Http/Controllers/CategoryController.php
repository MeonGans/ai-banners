<?php

namespace App\Http\Controllers;

use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Models\Banner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Date\Date;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        //Вывод всех категорий, отображаем какие из них в премиум группе,
        // а какие в индивидуальной с привязкой к пользователю

        $categories = Category::query()->with(['group', 'user'])->get();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        //Добавление новой категории
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = $request->all();
        $category = Category::query()->create($data);

        return $this->sendResponse($category, 'Category create successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show($category_id, $option = null): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        //random, news, premium, popular
        $banners = Banner::query()->where('category_id', $category_id);

        //Отображение баннеров выбраной категории + информация о самой категории
        switch ($option) {
            case 'random':
                $banners->inRandomOrder();
                break;
            case 'premium':
                $banners->where('is_premium', true)->orderByDesc('updated_at');
                break;
            case 'popular':
                $banners->orderByDesc('used');
                break;
            default:
                $banners->orderByDesc('updated_at');
        }
        return BannerResource::collection($banners->get());


        //Проверка отображения, нельзя выводить пользователю без премиума
        // премиальную категорию, возврат ошибки "нет доступа"
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        //Обновление информации о выбраной категории (група, название и прочее)
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'group_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = $request->all();
        $category = Category::query()->find($id)->update($data);

        return $this->sendResponse($category, 'Category update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): \Illuminate\Http\Response
    {
        $category = Category::destroy($id);
        return $this->sendResponse($category, 'Category delete successfully.');
    }

    public function restore($id): \Illuminate\Http\JsonResponse
    {
        $category = Category::withTrashed()->find($id)->restore();
        return $this->sendResponse($category, 'Category restore successfully.');
    }
}
