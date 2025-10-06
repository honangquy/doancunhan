<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chair.conferences.index', [
            'title' => 'Quản lý Hội thảo'
        ]);
    }

    public function show($id)
    {
        return view('chair.conferences.show', [
            'title' => 'Chi tiết Hội thảo',
            'conferenceId' => $id
        ]);
    }
}
