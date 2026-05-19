<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApplicationService;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    const ITEMS_PER_PAGE = 10;

    public function __construct(private ApplicationService $service) {}

    public function index(Request $request, ?string $plan = null)
    {
        $per_page = $request->input('per_page') ?? self::ITEMS_PER_PAGE;

        $result = $this->service->getApplicationsByPlan($plan);

        if (!$result->exists()) {
            return response()->json([
                'success' => false,
                'message' => "No applications " . ($plan ? "of type '$plan' " : '') . "found.",
            ], 404);
        }

        return $result->paginate($per_page)
            ->appends(['per_page' => $per_page])
            ->toResourceCollection();
    }
}
