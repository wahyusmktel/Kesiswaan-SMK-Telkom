<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    /**
     * Tampilkan halaman utama dokumentasi.
     */
    public function index()
    {
        return view('pages.docs.index');
    }

    /**
     * Tampilkan panduan penggunaan untuk Guru Piket.
     */
    public function piket()
    {
        return view('pages.docs.piket');
    }
}
