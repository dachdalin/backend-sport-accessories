<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ApiDocumentationService;
use Inertia\Inertia;
use Inertia\Response;

class ApiDocumentationController extends Controller
{
    /**
     * Display the customer API's documentation and live testing console.
     */
    public function index(ApiDocumentationService $apiDocumentationService): Response
    {
        return Inertia::render('api-documentation/Index', [
            'groups' => $apiDocumentationService->groups(),
        ]);
    }
}
