<?php

namespace App\Actions\Testimonials;

use App\Models\Testimonial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteTestimonialAction
{
    public function handle(Testimonial $testimonial): void
    {
        $path = $testimonial->avatar;
        $disk = $testimonial->avatar_storage_type;

        DB::transaction(function () use ($testimonial) {
            $testimonial->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
