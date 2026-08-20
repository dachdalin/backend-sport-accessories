<?php

namespace App\Actions\Banners;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateBannerAction
{
    /**
     * @param  array{title: string, image_alt_text: ?string, link_url: ?string, sort_order?: int, status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $image): Banner
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $image, &$storedPath) {
                if ($image) {
                    $storedPath = $image->store('banners', 'public');
                    $data['image'] = $storedPath;
                }

                return Banner::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $e;
        }
    }
}
