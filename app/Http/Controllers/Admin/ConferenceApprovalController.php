<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HoiThao;
use Illuminate\Http\Request;

class ConferenceApprovalController extends Controller
{
    /**
     * Display conferences pending approval
     */
    public function index()
    {
        $conferences = HoiThao::where('status', 'PENDING_ADMIN_APPROVAL')
            ->with(['user', 'chair'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.conferences.approval.index', compact('conferences'));
    }

    /**
     * Show conference details for approval
     */
    public function show($conferenceId)
    {
        $conference = HoiThao::where('conference_id', $conferenceId)
            ->where('status', 'PENDING_ADMIN_APPROVAL')
            ->with(['user', 'chair', 'tieuban'])
            ->firstOrFail();

        return view('admin.conferences.approval.show', compact('conference'));
    }

    /**
     * Approve conference
     */
    public function approve(Request $request, $conferenceId)
    {
        $conference = HoiThao::where('conference_id', $conferenceId)
            ->where('status', 'PENDING_ADMIN_APPROVAL')
            ->firstOrFail();

        \DB::transaction(function () use ($conference, $request) {
            // Update conference status
            $conference->update([
                'status' => 'ACTIVE',
                'approved_by' => auth()->user()->user_id,
                'approved_at' => now(),
                'approval_note' => $request->input('approval_note')
            ]);

            // Assign CHAIR role to the conference chair
            \DB::table('vaitronguoidung')->updateOrInsert(
                [
                    'user_id' => $conference->chair_id,
                    'role_code' => 'CHAIR',
                    'conference_id' => $conference->conference_id
                ]
            );

            \Log::info('Conference approved and chair role assigned', [
                'conference_id' => $conference->conference_id,
                'chair_id' => $conference->chair_id,
                'approved_by' => auth()->user()->user_id
            ]);
        });

        return redirect()->route('admin.conferences.approval.index')
            ->with('success', 'Hội thảo đã được phê duyệt và kích hoạt thành công. Chair đã được gán quyền quản lý.');
    }

    /**
     * Reject conference
     */
    public function reject(Request $request, $conferenceId)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $conference = HoiThao::where('conference_id', $conferenceId)
            ->where('status', 'PENDING_ADMIN_APPROVAL')
            ->firstOrFail();

        $conference->update([
            'status' => 'REJECTED',
            'approved_by' => auth()->user()->user_id,
            'approved_at' => now(),
            'approval_note' => $request->input('rejection_reason')
        ]);

        return redirect()->route('admin.conferences.approval.index')
            ->with('success', 'Hội thảo đã bị từ chối.');
    }
}




