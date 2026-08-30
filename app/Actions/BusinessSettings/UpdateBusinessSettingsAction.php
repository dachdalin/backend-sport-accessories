<?php

namespace App\Actions\BusinessSettings;

use App\Services\BusinessSettingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateBusinessSettingsAction
{
    public function __construct(private readonly BusinessSettingService $businessSettings) {}

    /**
     * @param  array<string, string>  $data
     */
    public function handle(array $data, ?UploadedFile $logo): void
    {
        $newPath = null;
        $current = $this->businessSettings->all();
        $oldPath = $current['logo'];
        $oldDisk = $current['logo_storage_type'];

        try {
            DB::transaction(function () use ($data, $logo, &$newPath) {
                if ($logo) {
                    $newPath = $logo->store('business-settings', 'cloudinary');
                    $data['logo'] = $newPath;
                    $data['logo_storage_type'] = 'cloudinary';
                }

                $this->businessSettings->save($data);
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
    }
}
