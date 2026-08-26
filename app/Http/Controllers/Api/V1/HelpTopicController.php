<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\HelpTopicResource;
use App\Models\HelpTopic;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HelpTopicController extends Controller
{
    /**
     * Display a paginated listing of the active help topics.
     */
    public function index(): AnonymousResourceCollection
    {
        return HelpTopicResource::collection(
            HelpTopic::query()->where('status', true)->orderBy('ranking')->paginate(15)->withQueryString(),
        );
    }

    /**
     * Display the specified active help topic.
     */
    public function show(HelpTopic $helpTopic): HelpTopicResource
    {
        abort_unless($helpTopic->status, 404);

        return new HelpTopicResource($helpTopic);
    }
}
