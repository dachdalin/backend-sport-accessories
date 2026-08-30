<?php

namespace App\Actions\TeamMembers;

use App\Models\TeamMember;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateTeamMemberAction
{
    /**
     * @param  array{name: string, role: string, bio: ?string, photo_alt_text: ?string, sort_order?: int, status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $photo): TeamMember
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $photo, &$storedPath) {
                if ($photo) {
                    $storedPath = $photo->store('team-members', 'cloudinary');
                    $data['photo'] = $storedPath;
                    $data['photo_storage_type'] = 'cloudinary';
                }

                return TeamMember::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('cloudinary')->delete($storedPath);
            }

            throw $e;
        }
    }
}
