<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', [
            'title' => 'Trang chủ - HUIT Conferences'
        ]);
    }

    public function conferences()
    {
        return view('conferences.index', [
            'title' => 'Danh sách Hội thảo'
        ]);
    }

    public function conferenceDetail($id)
    {
        return view('conferences.show', [
            'title' => 'Chi tiết Hội thảo',
            'conferenceId' => $id
        ]);
    }

    public function news()
    {
        return view('news.index', [
            'title' => 'Tin tức & Sự kiện'
        ]);
    }

    public function process()
    {
        return view('process', [
            'title' => 'Quy trình'
        ]);
    }

    public function support()
    {
        return view('support', [
            'title' => 'Hỗ trợ'
        ]);
    }
}
