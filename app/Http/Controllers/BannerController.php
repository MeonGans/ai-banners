<?php

namespace App\Http\Controllers;

use App\Http\Requests\Banner\StoreRequest;
use App\Http\Requests\Banner\UpdateRequest;
use App\Http\Resources\BannerCollection;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    protected Banner $banner;

    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
    }

    /**
     * Display a listing of the resource.
     *
     * @return BannerCollection
     */
    public function index(): BannerCollection
    {
        $banners = $this->banner->with('category', 'files')->orderByDesc('created_at');
        return new BannerCollection($banners->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return BannerResource
     */
    public function store(StoreRequest $request): BannerResource
    {
        //Add banner
        $request->validated();
        $data = $request->all();

        $data['data'] = json_encode($data['data']); //preview basic base64

        $data['preview'] = $this->uploadBase64($data['preview']);
        $collection = collect($data['files']);
        $collection = $collection->map(function ($values) {
            return $values['id'];
        });
        $banner = Banner::query()->create($data);
        $banner->files()->sync($collection->all() ?? []);

        return new BannerResource($banner);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Banner $banner
     * @return BannerResource
     */
    public function show(Banner $banner): BannerResource
    {
        event('bannerHasViewed', $banner);
        return new BannerResource($banner);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Banner $banner
     * @return BannerResource
     */
    public function update(UpdateRequest $request, $banner_id): BannerResource
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
        $collection = collect($data['files']);
        $collection = $collection->map(function ($values) {
            // dd($values['id']);
            return $values['id'];
        });
        $banner->update($data);
        $banner->files()->sync($collection->all() ?? []);

        return new BannerResource($banner);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Banner $banner
     * @return void
     */
    public function destroy(Banner $banner): void
    {
        $banner->delete();
    }

    public function restore($id): BannerResource
    {
        $banner = Banner::withTrashed()->find($id)->restore();
        return new BannerResource($banner);
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

//    public function makeDirectory(): int
//    {
//        $random = rand(10, 9999);
//        Storage::makeDirectory($random);
//        return $random;
//    }
}
