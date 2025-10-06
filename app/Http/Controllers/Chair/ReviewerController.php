<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReviewerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('chair.reviewers.index', [
            'title' => 'Quản lý Reviewer'
        ]);
    }
}
