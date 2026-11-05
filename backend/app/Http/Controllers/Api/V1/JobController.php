<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\ApiJob;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function show(Request $request, string $id)
    {
        $job = ApiJob::findOrFail($id);

        return response()->json($job->toApiResponse());
    }
}
