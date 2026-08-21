<?php

namespace App\Actions\EmailTemplates;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;

class CreateEmailTemplateAction
{
    /**
     * @param  array{name: string, subject: string, body: string, status: bool}  $data
     */
    public function handle(array $data): EmailTemplate
    {
        return DB::transaction(fn () => EmailTemplate::create($data));
    }
}
