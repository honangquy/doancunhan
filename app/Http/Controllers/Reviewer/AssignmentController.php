<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('reviewer.assignments.index', [
            'title' => 'Bài được phân công'
        ]);
    }

    public function show($id)
    {
        return view('reviewer.assignments.show', [
            'title' => 'Chi tiết phân công',
            'assignmentId' => $id
        ]);
    }
}




