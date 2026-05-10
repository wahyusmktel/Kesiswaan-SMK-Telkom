<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class DigiReligiController extends Controller
{
    public function index()
    {
        return view('public.digireligi');
    }
}
