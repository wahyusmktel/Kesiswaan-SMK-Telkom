<?php

namespace App\Http\Controllers\Erapor;

use App\Http\Controllers\Controller;
use App\Services\Erapor\EraporReadinessService;
use Illuminate\Contracts\View\View;

class LandingController extends Controller
{
    public function __invoke(EraporReadinessService $readiness): View
    {
        return view('pages.erapor.index', $readiness->inspect());
    }
}
