<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chair.papers.index', [
            'title' => 'Quản lý Bài báo'
        ]);
    }

    public function show($id)
    {
        return view('chair.papers.show', [
            'title' => 'Chi tiết Bài báo',
            'paperId' => $id
        ]);
    }
}




