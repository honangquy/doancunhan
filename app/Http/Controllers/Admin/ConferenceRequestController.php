<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\YeuCauHoiThao;
use App\Models\HoiThao;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConferenceRequestController extends Controller
{
    /**
     * Display a listing of conference requests.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        
        $query = YeuCauHoiThao::with(['user', 'requester', 'coChairs'])
            ->orderBy('created_at', 'desc');
            
        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('chair_fullname', 'like', "%{$search}%")
                  ->orWhere('chair_email', 'like', "%{$search}%")
                  ->orWhere('field', 'like', "%{$search}%");
            });
        }
        
        $requests = $query->paginate(15);
        
        $stats = [
            'total' => YeuCauHoiThao::count(),
            'pending' => YeuCauHoiThao::where('status', 'PENDING')->count(),
            'approved' => YeuCauHoiThao::where('status', 'APPROVED')->count(),
            'rejected' => YeuCauHoiThao::where('status', 'REJECTED')->count(),
        ];
        
        return view('admin.conference-requests.index', compact('requests', 'stats', 'status', 'search'));
    }

    /**
     * Display configured conferences awaiting final approval
     */
    public function configuredConferences(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        
        $query = HoiThao::with(['chair', 'conferenceRequest', 'committees'])
            ->whereNotNull('chair_id')
            ->orderBy('conference_id', 'desc'); // Sử dụng conference_id thay vì created_at
            
        if ($status !== 'all') {
            $query->where('status', strtoupper($status));
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('conference_name', 'like', "%{$search}%")
                  ->orWhere('conference_acronym', 'like', "%{$search}%")
                  ->orWhere('field', 'like', "%{$search}%")
                  ->orWhereHas('chair', function($subQ) use ($search) {
                      $subQ->where('full_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $conferences = $query->paginate(15);
        
        $stats = [
            'total' => HoiThao::whereNotNull('chair_id')->count(),
            'pending' => HoiThao::where('status', 'PENDING_ADMIN_APPROVAL')->count(),
            'active' => HoiThao::where('status', 'ACTIVE')->count(),
            'inactive' => HoiThao::where('status', 'INACTIVE')->count(),
        ];
        
        return view('admin.configured-conferences.index', compact('conferences', 'stats', 'status', 'search'));
    }

    /**
     * Display the specified conference request.
     */
    public function show($id)
    {
        $request = YeuCauHoiThao::with(['user', 'requester', 'coChairs'])->findOrFail($id);
        
        return view('admin.conference-requests.show', compact('request'));
    }

    /**
     * Approve a conference request.
     */
    public function approve(Request $request, $id)
    {
        $conferenceRequest = YeuCauHoiThao::findOrFail($id);
        
        $request->validate([
            'approval_note' => 'nullable|string|max:1000',
        ]);
        
        $conferenceRequest->update([
            'status' => 'APPROVED',
            'approver_id' => auth()->user()->user_id,
            'approval_note' => $request->approval_note,
            'approved_at' => now(),
        ]);
        
        // Grant CHAIR role to the requester
        if ($conferenceRequest->user) {
            $user = $conferenceRequest->user;
            // Ensure user has CHAIR role (global CHAIR role for conference management)
            if (!$user->hasRole('CHAIR') && !$user->hasRole('ADMIN')) {
                $user->assignRole('CHAIR');
            }
        }
        
        // TODO: Send notification email to the requester
        
        return redirect()->route('admin.conference-requests.index')
                        ->with('success', 'Yêu cầu đã được phê duyệt thành công. Người dùng đã được cấp quyền Chair.');
    }

    /**
     * Reject a conference request.
     */
    public function reject(Request $request, $id)
    {
        $conferenceRequest = YeuCauHoiThao::findOrFail($id);
        
        $request->validate([
            'approval_note' => 'required|string|max:1000',
        ]);
        
        $conferenceRequest->update([
            'status' => 'REJECTED',
            'approver_id' => auth()->user()->user_id,
            'approval_note' => $request->approval_note,
            'approved_at' => now(),
        ]);
        
        // TODO: Send notification email to the requester
        
        return redirect()->route('admin.conference-requests.index')
                        ->with('success', 'Yêu cầu đã bị từ chối.');
    }

    /**
     * Download the proposal file.
     */
    public function downloadProposal($id)
    {
        $conferenceRequest = YeuCauHoiThao::findOrFail($id);
        
        if (!$conferenceRequest->proposal_file) {
            abort(404, 'File không tồn tại.');
        }
        
        $filePath = storage_path('app/public/conference-requests/' . $conferenceRequest->proposal_file);
        
        if (!file_exists($filePath)) {
            abort(404, 'File không tồn tại.');
        }
        
        return response()->download($filePath, 'proposal_' . $conferenceRequest->request_id . '.pdf');
    }

    /**
     * Bulk action for conference requests.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:yeucauhoithao,request_id',
            'bulk_note' => 'nullable|string|max:1000',
        ]);
        
        $ids = $request->ids;
        $action = $request->action;
        $note = $request->bulk_note;
        
        foreach ($ids as $id) {
            $conferenceRequest = YeuCauHoiThao::find($id);
            
            if (!$conferenceRequest) continue;
            
            switch ($action) {
                case 'approve':
                    $conferenceRequest->update([
                        'status' => 'APPROVED',
                        'approver_id' => auth()->user()->user_id,
                        'approval_note' => $note,
                        'approved_at' => now(),
                    ]);
                    break;
                    
                case 'reject':
                    $conferenceRequest->update([
                        'status' => 'REJECTED',
                        'approver_id' => auth()->user()->user_id,
                        'approval_note' => $note,
                        'approved_at' => now(),
                    ]);
                    break;
                    
                case 'delete':
                    // Delete proposal file if exists
                    if ($conferenceRequest->proposal_file) {
                        Storage::disk('public')->delete('conference-requests/' . $conferenceRequest->proposal_file);
                    }
                    $conferenceRequest->delete();
                    break;
            }
        }
        
        $actionText = [
            'approve' => 'phê duyệt',
            'reject' => 'từ chối',
            'delete' => 'xóa'
        ];
        
        return redirect()->route('admin.conference-requests.index')
                        ->with('success', 'Đã ' . $actionText[$action] . ' ' . count($ids) . ' yêu cầu.');
    }

    /**
     * Show configured conference for final approval
     */
    public function showConference($conferenceId)
    {
        $conference = HoiThao::with(['chair', 'conferenceRequest', 'committees'])->findOrFail($conferenceId);
        
        return view('admin.conferences.show', compact('conference'));
    }

    /**
     * Approve and activate conference
     */
    public function approveConference(Request $request, $conferenceId)
    {
        \Log::info('ApproveConference called', [
            'conferenceId' => $conferenceId,
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url()
        ]);
        
        $conference = HoiThao::findOrFail($conferenceId);
        
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);

        // Use transaction to ensure both conference approval and role assignment succeed
        \DB::transaction(function () use ($conference, $request) {
            $conference->update([
                'status' => 'ACTIVE',
                'admin_approver_id' => auth()->user()->user_id,
                'admin_note' => $request->admin_note,
                'admin_approved_at' => now(),
                'approved_by' => auth()->user()->user_id,
                'approved_at' => now(),
            ]);

            // Assign CHAIR role for this specific conference
            if ($conference->chair_id) {
                \DB::table('vaitronguoidung')->updateOrInsert(
                    [
                        'user_id' => $conference->chair_id,
                        'role_code' => 'CHAIR',
                        'conference_id' => $conference->conference_id
                    ]
                );
                
                \Log::info('Chair role assigned', [
                    'conference_id' => $conference->conference_id,
                    'chair_id' => $conference->chair_id
                ]);
            }
        });

        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hội thảo đã được kích hoạt và Chair đã được phân quyền.',
                'conference' => $conference
            ]);
        }

        return redirect()->route('admin.configured-conferences.index')
                        ->with('success', 'Hội thảo đã được kích hoạt và Chair đã được phân quyền.');
    }

    /**
     * Reject conference configuration
     */
    public function rejectConference(Request $request, $conferenceId)
    {
        $conference = HoiThao::findOrFail($conferenceId);
        
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);
        
        $conference->update([
            'status' => 'REJECTED',
            'admin_approver_id' => auth()->user()->user_id,
            'admin_note' => $request->reason,
            'admin_approved_at' => now(),
        ]);
        
        // Return JSON response for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cấu hình hội thảo đã bị từ chối.',
                'conference' => $conference
            ]);
        }
        
        return redirect()->route('admin.configured-conferences.index')
                        ->with('success', 'Cấu hình hội thảo đã bị từ chối.');
    }

    /**
     * Display all active conferences
     */
    public function allConferences(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $year = $request->get('year');
        $level = $request->get('level');
        
        $query = HoiThao::with(['chair', 'conferenceRequest'])
            ->orderBy('conference_id', 'desc');
            
        // Status filter
        if ($status === 'all') {
            // Show all conferences
        } elseif ($status === 'active') {
            $query->where('status', 'ACTIVE');
        } else {
            $query->where('status', strtoupper($status));
        }
        
        // Year filter
        if ($year) {
            $query->where('year', $year);
        }
        
        // Level filter
        if ($level) {
            $query->where('level_code', $level);
        }
        
        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('acronym', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('chair', function($subQ) use ($search) {
                      $subQ->where('full_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $conferences = $query->paginate(15);
        
        return view('admin.conferences.index', compact('conferences', 'search', 'status', 'year', 'level'));
    }

    /**
     * Show conference details for admin
     */
    public function showConferenceDetails($id)
    {
        $conference = HoiThao::with(['chair', 'conferenceRequest', 'baiBaos', 'tieuBans'])
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $conference->conference_id,
                'title' => $conference->title,
                'acronym' => $conference->acronym,
                'description' => $conference->description,
                'status' => $conference->status,
                'level' => $conference->level_code,
                'year' => $conference->year,
                'start_date' => $conference->start_date ? $conference->start_date->format('Y-m-d') : null,
                'end_date' => $conference->end_date ? $conference->end_date->format('Y-m-d') : null,
                'location' => $conference->location,
                'chair' => $conference->chair ? [
                    'name' => $conference->chair->full_name,
                    'email' => $conference->chair->email
                ] : null,
                'statistics' => [
                    'papers_count' => $conference->baiBaos()->count(),
                    'committees_count' => $conference->tieuBans()->count(),
                ],
                'dates' => [
                    'submission_deadline' => $conference->deadline_submission ? $conference->deadline_submission->format('Y-m-d') : null,
                    'review_deadline' => $conference->deadline_review ? $conference->deadline_review->format('Y-m-d') : null,
                    'camera_ready_deadline' => $conference->deadline_camera_ready ? $conference->deadline_camera_ready->format('Y-m-d') : null,
                ]
            ]
        ]);
    }

    /**
     * Show edit form for conference
     */
    public function editConference($id)
    {
        $conference = HoiThao::with(['chair', 'conferenceRequest'])->findOrFail($id);
        
        return view('admin.conferences.edit', compact('conference'));
    }

    /**
     * Update conference
     */
    public function updateConference(Request $request, $id)
    {
        $conference = HoiThao::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'acronym' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'deadline_submission' => 'nullable|date',
            'deadline_review' => 'nullable|date',
            'deadline_camera_ready' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'level_code' => 'nullable|in:INTERNATIONAL,NATIONAL,REGIONAL,INSTITUTIONAL',
            'year' => 'nullable|integer|min:2020|max:2030',
        ]);

        $conference->update([
            'title' => $request->title,
            'acronym' => $request->acronym,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'deadline_submission' => $request->deadline_submission,
            'deadline_review' => $request->deadline_review,
            'deadline_camera_ready' => $request->deadline_camera_ready,
            'location' => $request->location,
            'level_code' => $request->level_code,
            'year' => $request->year,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật hội thảo thành công!',
            'data' => $conference
        ]);
    }

    /**
     * Change conference status
     */
    public function changeConferenceStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,PENDING,COMPLETED'
        ]);

        $conference = HoiThao::findOrFail($id);
        $conference->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thay đổi trạng thái thành công!',
            'data' => [
                'id' => $conference->conference_id,
                'status' => $conference->status
            ]
        ]);
    }

    /**
     * Delete conference
     */
    public function deleteConference($id)
    {
        try {
            $conference = HoiThao::findOrFail($id);
            
            // Check if conference has associated data
            $hasSubmissions = $conference->baiBaos()->count() > 0;
            $hasJoinRequests = $conference->joinRequests()->count() > 0;
            $hasCommittees = $conference->committees()->count() > 0;
            
            if ($hasSubmissions || $hasJoinRequests || $hasCommittees) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa hội thảo vì đã có bài báo, yêu cầu tham gia hoặc ban tổ chức đăng ký!'
                ], 422);
            }
            
            $title = $conference->title;
            $conference->delete();

            return response()->json([
                'success' => true,
                'message' => "Đã xóa hội thảo '{$title}' thành công!"
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa hội thảo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete conferences
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:hoithao,conference_id'
        ]);

        try {
            $deletedCount = 0;
            $failedCount = 0;
            $conferences = HoiThao::whereIn('conference_id', $request->ids)->get();
            
            foreach ($conferences as $conference) {
                // Check if conference has associated data
                $hasSubmissions = $conference->baiBaos()->count() > 0;
                $hasJoinRequests = $conference->joinRequests()->count() > 0;
                $hasCommittees = $conference->committees()->count() > 0;
                
                if (!$hasSubmissions && !$hasJoinRequests && !$hasCommittees) {
                    $conference->delete();
                    $deletedCount++;
                } else {
                    $failedCount++;
                }
            }

            $message = "Đã xóa {$deletedCount} hội thảo.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} hội thảo không thể xóa vì đã có dữ liệu liên quan.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'deleted' => $deletedCount,
                    'failed' => $failedCount
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa hội thảo: ' . $e->getMessage()
            ], 500);
        }
    }
}



