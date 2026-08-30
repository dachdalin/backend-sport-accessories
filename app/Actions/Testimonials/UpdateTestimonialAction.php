<?php

namespace App\Actions\Testimonials;

use App\Models\Testimonial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateTestimonialAction
{
    /**
     * @param  array{customer_name: string, customer_role: ?string, content: string, rating: int, status: bool}  $data
     */
    public function handle(Testimonial $testimonial, array $data, ?UploadedFile $avatar): Testimonial
    {
        $newPath = null;
        $oldPath = $testimonial->avatar;
        $oldDisk = $testimonial->avatar_storage_type;

        try {
            $testimonial = DB::transaction(function () use ($testimonial, $data, $avatar, &$newPath) {
                if ($avatar) {
                    $newPath = $avatar->store('testimonials', 'cloudinary');
                    $data['avatar'] = $newPath;
                    $data['avatar_storage_type'] = 'cloudinary';
                }

                $testimonial->update($data);

                return $testimonial;
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

        return $testimonial;
    }
}
