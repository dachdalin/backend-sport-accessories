<?php

namespace App\Actions\HelpTopics;

use App\Models\HelpTopic;

class CreateHelpTopicAction
{
    /**
     * @param  array{type?: string, question: string, answer: string, ranking?: int, status?: bool}  $data
     */
    public function handle(array $data): HelpTopic
    {
        return HelpTopic::create($data);
    }
}
