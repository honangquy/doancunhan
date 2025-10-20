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
        
        $query = YeuCauHoiThao::with(['user', 'requester'])
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
            ->orderBy('created_at', 'desc');
            
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
        $request = YeuCauHoiThao::with(['user', 'requester'])->findOrFail($id);
        
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
        $conference = HoiThao::findOrFail($conferenceId);
        
        $request->validate([
            'admin_note' => 'nullable|string|max:1000',
        ]);
        
        $conference->update([
            'status' => 'ACTIVE',
            'admin_approver_id' => auth()->user()->user_id,
            'admin_note' => $request->admin_note,
            'admin_approved_at' => now(),
        ]);
        
        // TODO: Send notification email to chair
        
        return redirect()->route('admin.configured-conferences.index')
                        ->with('success', 'Hội thảo đã được kích hoạt và hiển thị trên trang chủ.');
    }

    /**
     * Reject conference configuration
     */
    public function rejectConference(Request $request, $conferenceId)
    {
        $conference = HoiThao::findOrFail($conferenceId);
        
        $request->validate([
            'admin_note' => 'required|string|max:1000',
        ]);
        
        $conference->update([
            'status' => 'REJECTED',
            'admin_approver_id' => auth()->user()->user_id,
            'admin_note' => $request->admin_note,
            'admin_approved_at' => now(),
        ]);
        
        // TODO: Send notification email to chair
        
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
        
        $query = HoiThao::with(['chair', 'conferenceRequest'])
            ->orderBy('created_at', 'desc');
            
        // Default to only show ACTIVE conferences, but allow filtering
        if ($status === 'all') {
            // Show all conferences
        } elseif ($status === 'active') {
            $query->where('status', 'ACTIVE');
        } else {
            $query->where('status', strtoupper($status));
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('conference_name', 'like', "%{$search}%")
                  ->orWhere('acronym', 'like', "%{$search}%")
                  ->orWhereHas('chair', function($subQ) use ($search) {
                      $subQ->where('full_name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $conferences = $query->paginate(15);
        
        return view('admin.conferences.index', compact('conferences', 'search', 'status'));
    }
}