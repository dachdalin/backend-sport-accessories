<?php

namespace App\Actions\Testimonials;

use App\Models\Testimonial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateTestimonialAction
{
    /**
     * @param  array{customer_name: string, customer_role: ?string, content: string, rating: int, status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $avatar): Testimonial
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $avatar, &$storedPath) {
                if ($avatar) {
                    $storedPath = $avatar->store('testimonials', 'cloudinary');
                    $data['avatar'] = $storedPath;
                    $data['avatar_storage_type'] = 'cloudinary';
                }

                return Testimonial::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('cloudinary')->delete($storedPath);
            }

            throw $e;
        }
    }
}
