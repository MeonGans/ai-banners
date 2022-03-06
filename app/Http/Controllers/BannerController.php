<?php

namespace App\Http\Controllers;

use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): \Illuminate\Http\Response
    {
        //Все баннеры нам вроде не надо
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        //Добавляем баннер.

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'data' => 'required',
            'preview' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = $request->all();

//        Создаем превью на основе base64 и сохраняем на диске

        $data['preview'] = $this->uploadBase64($data['preview']);

        $banner = Banner::query()->create($data);

        return $this->sendResponse($banner, 'Banner create successfully.');

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
    public function update(Request $request, $banner_id): \Illuminate\Http\JsonResponse
    {
        //Обновляем информацию о баннере, заново декодируем превью, сохраняем его, а старое удаляем.
        $validator = Validator::make($request->all(), [
            'data' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = $request->all();

        $banner = Banner::query()->find($banner_id);

        if (isset($data['preview'])) {
            Storage::delete($banner->preview);

            $data['preview'] = $this->uploadBase64($data['preview']);
        }
        $banner->update($data);

        return $this->sendResponse($banner, 'Banner update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $banner_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($banner_id): \Illuminate\Http\JsonResponse
    {
        //Софт, удаляем конкретный баннер
        $banner = Banner::destroy($banner_id);
        return $this->sendResponse($banner, 'Banner delete successfully.');
    }

    static function uploadBase64($image): string
    {
        $folderPath = 'preview/' . date('Y-m-d') . '/' ;
        //$image = $data['preview'];
        $image = base64_decode($image);
        $file = $folderPath . uniqid() . '.png';
        Storage::put($file, $image);

        return $file;
    }
}
