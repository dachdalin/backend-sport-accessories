<?php

namespace App\Actions\HelpTopics;

use App\Models\HelpTopic;

class UpdateHelpTopicAction
{
    /**
     * @param  array{type?: string, question: string, answer: string, ranking?: int, status?: bool}  $data
     */
    public function handle(HelpTopic $helpTopic, array $data): HelpTopic
    {
        $helpTopic->update($data);

        return $helpTopic;
    }
}
