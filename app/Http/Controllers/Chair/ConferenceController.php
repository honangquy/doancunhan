<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\YeuCauHoiThao;
use App\Models\HoiThao;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display chair's conferences
     */
    public function myConferences()
    {
        $user = auth()->user();
        
        $conferences = HoiThao::where('chair_email', $user->email)
            ->orWhere('contact_email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('chair.my-conferences', [
            'title' => 'Hội thảo của tôi',
            'conferences' => $conferences
        ]);
    }

    /**
     * Show configuration form for approved request
     */
    public function configureForm($id)
    {
        $user = auth()->user();
        
        // Get the conference request
        $request = YeuCauHoiThao::findOrFail($id);

        // Check permission - only requester can configure
        if ($request->user_id !== $user->user_id) {
            abort(403, 'Unauthorized');
        }

        // Check if request is approved
        if ($request->status !== 'APPROVED') {
            abort(400, 'Yêu cầu phải ở trạng thái được duyệt');
        }

        return view('chair.configure-conference', [
            'title' => 'Cấu hình Hội thảo',
            'requestId' => $id,
            'request' => $request
        ]);
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




