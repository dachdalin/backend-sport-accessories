<?php

namespace App\Actions\TeamMembers;

use App\Models\TeamMember;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateTeamMemberAction
{
    /**
     * @param  array{name: string, role: string, bio: ?string, photo_alt_text: ?string, sort_order?: int, status: bool}  $data
     */
    public function handle(TeamMember $teamMember, array $data, ?UploadedFile $photo): TeamMember
    {
        $newPath = null;
        $oldPath = $teamMember->photo;
        $oldDisk = $teamMember->photo_storage_type;

        try {
            $teamMember = DB::transaction(function () use ($teamMember, $data, $photo, &$newPath) {
                if ($photo) {
                    $newPath = $photo->store('team-members', 'public');
                    $data['photo'] = $newPath;
                }

                $teamMember->update($data);

                return $teamMember;
            });
        } catch (Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $e;
        }

        if ($newPath && $oldPath !== 'def.png') {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $teamMember;
    }
}
