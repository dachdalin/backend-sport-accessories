<?php

namespace App\Actions\EmailTemplates;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;

class DeleteEmailTemplateAction
{
    public function handle(EmailTemplate $emailTemplate): void
    {
        DB::transaction(function () use ($emailTemplate) {
            $emailTemplate->delete();
        });
    }
}
