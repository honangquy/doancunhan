<?php

namespace App\Http\Controllers;

use App\Models\YeuCauHoiThao;
use App\Models\HoiThao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConferenceManagementController extends Controller
{
    /**
     * Display conference requests for management
     */
    public function requests(Request $request)
    {
        $user = Auth::user();
        
        // Build query based on user role and filters
        $query = YeuCauHoiThao::with(['requester', 'admin']);
        
        // Filter by role
        if (!$user->hasRole('ADMIN')) {
            // Non-admin users can only see their own requests
            $query->where('requester_id', $user->id);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by level_code
        if ($request->has('level_code') && $request->level_code !== '') {
            $query->where('level_code', $request->level_code);
        }
        
        // Search by title
        if ($request->has('search') && $request->search !== '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        // Order by latest
        $requests = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get statistics
        $stats = [
            'total' => YeuCauHoiThao::count(),
            'pending' => YeuCauHoiThao::where('status', 'PENDING')->count(),
            'approved' => YeuCauHoiThao::where('status', 'APPROVED')->count(),
            'rejected' => YeuCauHoiThao::where('status', 'REJECTED')->count(),
        ];
        
        if (!$user->hasRole('ADMIN')) {
            // Filter stats for non-admin users
            $stats = [
                'total' => YeuCauHoiThao::where('requester_id', $user->id)->count(),
                'pending' => YeuCauHoiThao::where('requester_id', $user->id)->where('status', 'PENDING')->count(),
                'approved' => YeuCauHoiThao::where('requester_id', $user->id)->where('status', 'APPROVED')->count(),
                'rejected' => YeuCauHoiThao::where('requester_id', $user->id)->where('status', 'REJECTED')->count(),
            ];
        }
        
        return view('conference-management.requests', compact('requests', 'stats'));
    }
    
    /**
     * Show specific conference request details
     */
    public function showRequest($id)
    {
        $user = Auth::user();
        
        $query = YeuCauHoiThao::with(['requester', 'admin', 'hoiThao']);
        
        // Non-admin users can only see their own requests
        if (!$user->hasRole('ADMIN')) {
            $query->where('requester_id', $user->id);
        }
        
        $request = $query->findOrFail($id);
        
        return view('conference-management.request-detail', compact('request'));
    }
    
    /**
     * Approve conference request (Admin only)
     */
    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('ADMIN')) {
            return redirect()->back()->with('error', 'Không có quyền thực hiện hành động này.');
        }
        
        $conferenceRequest = YeuCauHoiThao::findOrFail($id);
        
        if ($conferenceRequest->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Chỉ có thể duyệt yêu cầu đang chờ xử lý.');
        }
        
        $conferenceRequest->update([
            'status' => 'APPROVED',
            'admin_id' => $user->id,
            'approved_at' => now(),
            'admin_notes' => $request->input('admin_notes', '')
        ]);
        
        return redirect()->back()->with('success', 'Đã duyệt yêu cầu hội thảo thành công.');
    }
    
    /**
     * Reject conference request (Admin only)
     */
    public function rejectRequest(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('ADMIN')) {
            return redirect()->back()->with('error', 'Không có quyền thực hiện hành động này.');
        }
        
        $conferenceRequest = YeuCauHoiThao::findOrFail($id);
        
        if ($conferenceRequest->status !== 'PENDING') {
            return redirect()->back()->with('error', 'Chỉ có thể từ chối yêu cầu đang chờ xử lý.');
        }
        
        $conferenceRequest->update([
            'status' => 'REJECTED',
            'admin_id' => $user->id,
            'rejected_at' => now(),
            'admin_notes' => $request->input('admin_notes', '')
        ]);
        
        return redirect()->back()->with('success', 'Đã từ chối yêu cầu hội thảo.');
    }
}



