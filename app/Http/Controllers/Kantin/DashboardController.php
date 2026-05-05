<?php

namespace App\Http\Controllers\Kantin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the Kantin Dashboard.
     */
    public function index()
    {
        return view('pages.kantin.dashboard');
    }
}
