<?php

namespace App\Http\Controllers;

use App\Http\Requests\Banner\StoreRequest;
use App\Http\Requests\Banner\UpdateRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $banners = Banner::query()->with('category')->get();
        return BannerResource::collection($banners);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request): \Illuminate\Http\JsonResponse
    {
        //Добавляем баннер.
        $request->validated();
        $data = $request->all();

        $data['data'] = json_encode($data['data']);
//        Создаем превью на основе base64 и сохраняем на диске

        $data['preview'] = $this->uploadBase64($data['preview']);

        $banner = Banner::query()->create($data);

        return $this->sendResponse($banner, 'Banner created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Banner $banner
     * @return BannerResource
     */
    public function show($banner_id): BannerResource
    {
        //Показываем конкретный баннер
        $banner = Banner::query()->find($banner_id);
        event('bannerHasViewed', $banner);
        //ДОБАВИТЬ ПРОВЕРКУ ПРЕМИУМА ДЛЯ ПРЕМИУМ БАННЕРОВ И ПРЕМИУМ КАТЕГОРИЙ,
        // А ТАКЖЕ ДЛЯ ИНДИВИДУАЛЬНЫХ КАТЕГОРИЙ
        return new BannerResource($banner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Banner $banner
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $banner_id): \Illuminate\Http\JsonResponse
    {
        //Обновляем информацию о баннере, заново декодируем превью, сохраняем его, а старое удаляем.

        $request->validated();
        $data = $request->all();

        $banner = Banner::query()->find($banner_id);
        $data['data'] = json_encode($data['data']);
        if (isset($data['preview'])) {
            Storage::delete($banner->preview);

            $data['preview'] = $this->uploadBase64($data['preview']);
        }
        $banner->update($data);

        return $this->sendResponse($banner, 'Banner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $banner_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($banner_id): \Illuminate\Http\JsonResponse
    {
        $banner = Banner::destroy($banner_id);
        return $this->sendResponse($banner, 'Banner deleted successfully.');
    }

    public function restore($id): \Illuminate\Http\JsonResponse
    {
        $banner = Banner::withTrashed()->find($id)->restore();
        return $this->sendResponse($banner, 'Category restore successfully.');
    }

    static function uploadBase64($image): string
    {
        $folderPath = 'preview/' . date('Y-m-d') . '/';
        $image = base64_decode($image);
        $file = $folderPath . uniqid() . '.png';
        Storage::put($file, $image);
        $img = Image::make(public_path('storage/' . $file));

        $height = $img->height();
        $width = $img->width();
        if ($height >= 601) {
            $img->resize(600, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        if ($width >= 601) {
            $img->resize(null, 600, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $img->save(public_path('storage/' . $file));

        return $file;
    }
}
