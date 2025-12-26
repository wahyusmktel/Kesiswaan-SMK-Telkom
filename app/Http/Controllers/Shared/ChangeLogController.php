<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChangeLogController extends Controller
{
    public function index()
    {
        return view('pages.shared.changelog.index');
    }
}
