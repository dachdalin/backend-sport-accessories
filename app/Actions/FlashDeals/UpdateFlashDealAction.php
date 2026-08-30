<?php

namespace App\Actions\FlashDeals;

use App\Models\FlashDeal;
use App\Services\FlashDealSlugService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateFlashDealAction
{
    public function __construct(private readonly FlashDealSlugService $slugService) {}

    /**
     * @param  array{title: string, start_date: string, end_date: string, status: bool, featured: bool, background_color: ?string, text_color: ?string}  $data
     * @param  array<int, array{product_id: int, discount: float, discount_type: string}>  $items
     */
    public function handle(FlashDeal $flashDeal, array $data, array $items, ?UploadedFile $banner): FlashDeal
    {
        $newPath = null;
        $oldPath = $flashDeal->banner;
        $oldDisk = $flashDeal->banner_storage_type;

        try {
            $flashDeal = DB::transaction(function () use ($flashDeal, $data, $items, $banner, &$newPath) {
                if ($data['title'] !== $flashDeal->title) {
                    $data['slug'] = $this->slugService->generate($data['title'], $flashDeal->id);
                }

                if ($banner) {
                    $newPath = $banner->store('flash-deals', 'cloudinary');
                    $data['banner'] = $newPath;
                    $data['banner_storage_type'] = 'cloudinary';
                }

                $flashDeal->update($data);

                $flashDeal->items()->delete();
                $flashDeal->items()->createMany($items);

                return $flashDeal;
            });
        } catch (Throwable $e) {
            if ($newPath) {
                Storage::disk('cloudinary')->delete($newPath);
            }

            throw $e;
        }

        if ($newPath && $oldPath !== 'def.png') {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $flashDeal;
    }
}
