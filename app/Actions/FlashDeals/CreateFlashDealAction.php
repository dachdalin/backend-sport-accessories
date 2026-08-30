<?php

namespace App\Actions\FlashDeals;

use App\Models\FlashDeal;
use App\Services\FlashDealSlugService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateFlashDealAction
{
    public function __construct(private readonly FlashDealSlugService $slugService) {}

    /**
     * @param  array{title: string, start_date: string, end_date: string, status: bool, featured: bool, background_color: ?string, text_color: ?string}  $data
     * @param  array<int, array{product_id: int, discount: float, discount_type: string}>  $items
     */
    public function handle(array $data, array $items, ?UploadedFile $banner): FlashDeal
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $items, $banner, &$storedPath) {
                $data['slug'] = $this->slugService->generate($data['title']);

                if ($banner) {
                    $storedPath = $banner->store('flash-deals', 'cloudinary');
                    $data['banner'] = $storedPath;
                    $data['banner_storage_type'] = 'cloudinary';
                }

                $flashDeal = FlashDeal::create($data);

                $flashDeal->items()->createMany($items);

                return $flashDeal;
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('cloudinary')->delete($storedPath);
            }

            throw $e;
        }
    }
}
