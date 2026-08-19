<?php

namespace App\Actions\HelpTopics;

use App\Models\HelpTopic;

class DeleteHelpTopicAction
{
    public function handle(HelpTopic $helpTopic): void
    {
        $helpTopic->delete();
    }
}
