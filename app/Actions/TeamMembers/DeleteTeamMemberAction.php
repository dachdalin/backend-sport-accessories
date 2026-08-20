<?php

namespace App\Actions\TeamMembers;

use App\Models\TeamMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteTeamMemberAction
{
    public function handle(TeamMember $teamMember): void
    {
        $path = $teamMember->photo;
        $disk = $teamMember->photo_storage_type;

        DB::transaction(function () use ($teamMember) {
            $teamMember->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
