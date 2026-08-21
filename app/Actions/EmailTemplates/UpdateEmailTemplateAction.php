<?php

namespace App\Actions\EmailTemplates;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;

class UpdateEmailTemplateAction
{
    /**
     * @param  array{name: string, subject: string, body: string, status: bool}  $data
     */
    public function handle(EmailTemplate $emailTemplate, array $data): EmailTemplate
    {
        DB::transaction(function () use ($emailTemplate, $data) {
            $emailTemplate->update($data);
        });

        return $emailTemplate;
    }
}
