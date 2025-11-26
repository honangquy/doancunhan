<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiBao;
use App\Models\HoiThao;
use App\Models\TieuBan;
use App\Models\PhienBanBaiBao;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaperController extends Controller
{
    /**
     * Check if paper can be edited based on conference deadlines and paper status
     */
    private function canEditPaper($paper)
    {
        $now = Carbon::now();
        $submissionDeadline = Carbon::parse($paper->deadline_submission);
        $cameraReadyDeadline = $paper->deadline_camera_ready ? Carbon::parse($paper->deadline_camera_ready) : null;
        
        // Kiểm tra xem có reviewer nào đã hoàn thành review chưa
        if ($this->hasCompletedReviews($paper->paper_id)) {
            return ['can_edit' => false, 'reason' => 'Không thể chỉnh sửa khi đã có reviewer hoàn thành phản biện.'];
        }
        
        // Giai đoạn 1: TRƯỚC Deadline Nộp bài - cho phép chỉnh sửa
        if ($now->lt($submissionDeadline) && in_array($paper->status_code, ['DRAFT', 'SUBMITTED'])) {
            return ['can_edit' => true, 'reason' => ''];
        }
        
        // Giai đoạn 2: SAU Deadline Nộp bài hoặc đang review - KHÔNG cho phép
        if ($now->gte($submissionDeadline) && in_array($paper->status_code, ['SUBMITTED', 'UNDER_REVIEW'])) {
            return ['can_edit' => false, 'reason' => 'Đã quá hạn nộp bài hoặc bài đang được phản biện.'];
        }
        
        // Giai đoạn 3: Có kết quả review
        if (in_array($paper->status_code, ['ACCEPTED', 'REJECTED'])) {
            // Nếu bị từ chối - KHÔNG cho phép chỉnh sửa
            if ($paper->status_code === 'REJECTED') {
                return ['can_edit' => false, 'reason' => 'Bài báo đã bị từ chối, không thể chỉnh sửa.'];
            }
            
            // Nếu được chấp nhận - chỉ cho phép trong thời hạn camera-ready
            if ($paper->status_code === 'ACCEPTED') {
                if ($cameraReadyDeadline && $now->lt($cameraReadyDeadline)) {
                    return ['can_edit' => true, 'reason' => ''];
                } else {
                    return ['can_edit' => false, 'reason' => 'Đã quá hạn camera-ready hoặc chưa có deadline camera-ready.'];
                }
            }
        }
        
        // Các trạng thái khác (WITHDRAWN, etc.) - KHÔNG cho phép
        return ['can_edit' => false, 'reason' => 'Trạng thái bài báo không cho phép chỉnh sửa.'];
    }
    
    /**
     * Check if paper can be withdrawn based on conference deadlines and paper status
     */
    private function canWithdrawPaper($paper)
    {
        $now = Carbon::now();
        $submissionDeadline = Carbon::parse($paper->deadline_submission);
        
        // Kiểm tra xem có reviewer nào đã hoàn thành review chưa
        if ($this->hasCompletedReviews($paper->paper_id)) {
            return ['can_withdraw' => false, 'reason' => 'Không thể rút bài khi đã có reviewer hoàn thành phản biện.'];
        }
        
        // Giai đoạn 1: TRƯỚC Deadline Nộp bài - cho phép rút bài
        if ($now->lt($submissionDeadline) && in_array($paper->status_code, ['DRAFT', 'SUBMITTED'])) {
            return ['can_withdraw' => true, 'reason' => ''];
        }
        
        // Giai đoạn 2: SAU Deadline Nộp bài hoặc đang review - KHÔNG cho phép
        if ($now->gte($submissionDeadline) && in_array($paper->status_code, ['SUBMITTED', 'UNDER_REVIEW'])) {
            return ['can_withdraw' => false, 'reason' => 'Đã quá hạn nộp bài hoặc bài đang được phản biện.'];
        }
        
        // Giai đoạn 3: Có kết quả review - KHÔNG cho phép rút (cả ACCEPTED và REJECTED)
        if (in_array($paper->status_code, ['ACCEPTED', 'REJECTED'])) {
            return ['can_withdraw' => false, 'reason' => 'Không thể rút bài sau khi có kết quả phản biện.'];
        }
        
        // Các trạng thái khác - KHÔNG cho phép
        return ['can_withdraw' => false, 'reason' => 'Trạng thái bài báo không cho phép rút bài.'];
    }
    
    /**
     * Check if any reviewer has completed review for this paper
     */
    private function hasCompletedReviews($paperId)
    {
        // Check if there are completed reviews (submitted and not draft)
        $completedReviews = DB::table('reviewer_assignments as ra')
            ->join('phanbien as p', 'ra.id', '=', 'p.assignment_id')
            ->where('ra.paper_id', $paperId)
            ->where('p.is_draft', 0)
            ->whereNotNull('p.submitted_at')
            ->whereNotNull('ra.review_submitted_at')
            ->count();
            
        return $completedReviews > 0;
    }

    /**
     * Display a listing of papers
     * GET /api/papers
     */
    public function index(Request $request)
    {
        try {
            $query = BaiBao::with([
                'hoiThao:conference_id,title,year',
                'tieuBan:track_id,title',
                'submitter:user_id,full_name,email',
                'tacGias:user_id,full_name',
                'currentVersion:version_id,version_no,submitted_at'
            ]);

            // Filter by conference
            if ($request->has('conference_id')) {
                $query->where('conference_id', $request->conference_id);
            }

            // Filter by track
            if ($request->has('track_id')) {
                $query->where('track_id', $request->track_id);
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('status_code', $request->status);
            }

            // Filter by submitter
            if ($request->has('submitter_id')) {
                $query->where('submitter_id', $request->submitter_id);
            }

            // Search by title/abstract
            if ($request->has('search')) {
                $keyword = $request->search;
                $query->where(function($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                      ->orWhere('abstract', 'like', "%{$keyword}%");
                });
            }

            // Filter my papers (as author or submitter)
            if ($request->has('my_papers') && $request->my_papers) {
                $userId = auth()->id();
                $query->where(function($q) use ($userId) {
                    $q->where('submitter_id', $userId)
                      ->orWhereHas('tacGias', function($q2) use ($userId) {
                          $q2->where('user_id', $userId);
                      });
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $papers = $query->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách bài báo',
                'data' => $papers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit a new paper
     * POST /api/papers
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'conference_id' => 'required|integer|exists:hoithao,conference_id',
                'track_id' => 'nullable|integer|exists:TieuBan,track_id',
                'title' => 'required|string|max:500',
                'abstract' => 'required|string',
                'authors' => 'required|array|min:1',
                'authors.*.user_id' => 'nullable|integer|exists:nguoidung,user_id',
                'authors.*.full_name' => 'required_without:authors.*.user_id|string|max:200',
                'authors.*.email' => 'required_without:authors.*.user_id|email|max:255',
                'authors.*.organization' => 'nullable|string|max:255',
                'authors.*.is_contact' => 'boolean',
                'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check conference exists and is open for submission
            $conference = HoiThao::find($request->conference_id);
            if (!$conference) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hội thảo không tồn tại'
                ], 404);
            }

            if ($conference->status !== 'OPEN') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Hội thảo không đang mở nhận bài'
                ], 400);
            }

            // Check submission deadline
            if ($conference->deadline_submission && now()->isAfter($conference->deadline_submission)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Đã hết hạn nộp bài'
                ], 400);
            }

            // Check track belongs to conference
            if ($request->track_id) {
                $track = TieuBan::where('track_id', $request->track_id)
                    ->where('conference_id', $request->conference_id)
                    ->first();
                
                if (!$track) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Track không thuộc hội thảo này'
                    ], 400);
                }
            }

            DB::beginTransaction();

            // Create paper
            $paper = BaiBao::create([
                'conference_id' => $request->conference_id,
                'track_id' => $request->track_id,
                'submitter_id' => auth()->id(),
                'title' => $request->title,
                'abstract' => $request->abstract,
                'status_code' => 'SUBMITTED',
                'created_at' => now(),
            ]);

            // Upload file
            $file = $request->file('file');
            $fileName = 'paper_' . $paper->paper_id . '_v1_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('papers/' . $conference->conference_id, $fileName, 'public');

            // Create first version
            $version = PhienBanBaiBao::create([
                'paper_id' => $paper->paper_id,
                'version_no' => 1,
                'file_path' => $filePath,
                'submitted_at' => now(),
                'note' => 'Phiên bản nộp lần đầu',
            ]);

            // Update current_version_id
            $paper->update(['current_version_id' => $version->version_id]);

            // Add authors
            foreach ($request->authors as $index => $authorData) {
                if (isset($authorData['user_id'])) {
                    // Existing user
                    DB::table('tacgiabaibao')->insert([
                        'paper_id' => $paper->paper_id,
                        'user_id' => $authorData['user_id'],
                        'author_order' => $index + 1,
                        'is_contact' => $authorData['is_contact'] ?? 0,
                        'organization' => $authorData['organization'] ?? null,
                    ]);
                } else {
                    // Create new user for external author
                    $newUser = NguoiDung::create([
                        'email' => $authorData['email'],
                        'password_hash' => bcrypt('defaultpassword123'), // Default password
                        'full_name' => $authorData['full_name'],
                        'is_student' => 0,
                        'organization' => $authorData['organization'] ?? null,
                        'created_at' => now(),
                    ]);

                    DB::table('tacgiabaibao')->insert([
                        'paper_id' => $paper->paper_id,
                        'user_id' => $newUser->user_id,
                        'author_order' => $index + 1,
                        'is_contact' => $authorData['is_contact'] ?? 0,
                        'organization' => $authorData['organization'] ?? null,
                    ]);
                }
            }

            DB::commit();

            // Load relationships
            $paper->load([
                'hoiThao',
                'tieuBan',
                'submitter',
                'tacGias',
                'currentVersion'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Nộp bài báo thành công',
                'data' => $paper
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi nộp bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified paper with detailed info and permissions
     * GET /api/papers/{id}
     */
    public function show($id)
    {
        try {
            $userId = auth()->id();
            
            // Get paper details with conference info
            $paper = DB::table('baibao')
                ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
                ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
                ->where('baibao.paper_id', $id)
                ->where('baibao.submitter_id', $userId)
                ->select(
                    'baibao.*',
                    'hoithao.title as conference_title',
                    'hoithao.deadline_submission',
                    'hoithao.deadline_camera_ready',
                    'trangthaibaibao.status_name'
                )
                ->first();
            
            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo hoặc bạn không có quyền truy cập.'
                ], 404);
            }
            
            // Check permissions for edit and withdraw
            $editPermission = $this->canEditPaper($paper);
            $withdrawPermission = $this->canWithdrawPaper($paper);
            
            // Get authors
            $authors = DB::table('tacgiabaibao')
                ->join('nguoidung', 'tacgiabaibao.user_id', '=', 'nguoidung.user_id')
                ->where('tacgiabaibao.paper_id', $id)
                ->select(
                    'nguoidung.user_id',
                    'nguoidung.full_name',
                    'nguoidung.email',
                    'nguoidung.organization',
                    'tacgiabaibao.author_order',
                    'tacgiabaibao.is_contact'
                )
                ->orderBy('tacgiabaibao.author_order')
                ->get();
            
            // Get review assignments
            $assignments = DB::table('reviewer_assignments as ra')
                ->leftJoin('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
                ->where('ra.paper_id', $id)
                ->select(
                    'ra.id as assignment_id',
                    'ra.user_id', 
                    'ra.status',
                    'ra.assigned_at',
                    'ra.review_submitted_at',
                    'u.full_name as reviewer_name'
                )
                ->orderBy('ra.assigned_at')
                ->get();
            
            // Get completed reviews (only if paper is under review or later)
            $reviews = [];
            if (in_array($paper->status_code, ['UNDER_REVIEW', 'ACCEPTED', 'REJECTED'])) {
                $reviews = DB::table('phanbien as p')
                    ->join('reviewer_assignments as ra', 'p.assignment_id', '=', 'ra.id')
                    ->leftJoin('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
                    ->leftJoin('loaikhuyennghi as lkn', 'p.recommendation_code', '=', 'lkn.recommendation_code')
                    ->where('ra.paper_id', $id)
                    ->where('p.is_draft', 0)
                    ->whereNotNull('p.submitted_at')
                    ->select(
                        'p.review_id',
                        'p.total_score',
                        'p.recommendation_code',
                        'p.comment_author',
                        'p.submitted_at',
                        'p.score_novelty',
                        'p.score_relevance', 
                        'p.score_technical_quality',
                        'p.score_presentation',
                        'p.score_references',
                        'lkn.recommendation_name',
                        'u.full_name as reviewer_name'
                    )
                    ->orderBy('p.submitted_at')
                    ->get();
            }
            
            // Prepare response
            $response = [
                'paper' => $paper,
                'authors' => $authors,
                'assignments' => $assignments,
                'reviews' => $reviews,
                'permissions' => [
                    'can_edit' => $editPermission['can_edit'],
                    'edit_reason' => $editPermission['reason'],
                    'can_withdraw' => $withdrawPermission['can_withdraw'],
                    'withdraw_reason' => $withdrawPermission['reason']
                ],
                'formatted_dates' => [
                    'created_at' => Carbon::parse($paper->created_at)->format('d/m/Y H:i'),
                    'deadline_submission' => Carbon::parse($paper->deadline_submission)->format('d/m/Y'),
                    'deadline_camera_ready' => $paper->deadline_camera_ready ? Carbon::parse($paper->deadline_camera_ready)->format('d/m/Y') : null
                ]
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Chi tiết bài báo',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy chi tiết bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified paper with permission checks
     * PUT /api/papers/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $userId = auth()->id();
            
            // Validate request
            $validator = Validator::make($request->all(), [
                'conference_id' => 'sometimes|exists:hoithao,conference_id',
                'title' => 'sometimes|string|max:500',
                'abstract' => 'sometimes|string',
                'keywords' => 'sometimes|string|max:500',
                'track_id' => 'nullable|integer|exists:tieuban,track_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Get paper with conference deadline info
            $paper = DB::table('baibao')
                ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
                ->where('baibao.paper_id', $id)
                ->where('baibao.submitter_id', $userId)
                ->select(
                    'baibao.*',
                    'hoithao.deadline_submission',
                    'hoithao.deadline_camera_ready'
                )
                ->first();
            
            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo hoặc bạn không có quyền chỉnh sửa'
                ], 404);
            }
            
            // Check if can edit based on deadline and review logic
            $editPermission = $this->canEditPaper($paper);
            if (!$editPermission['can_edit']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $editPermission['reason']
                ], 403);
            }
            
            DB::beginTransaction();
            
            try {
                // Update paper
                $updateData = [];
                if ($request->has('conference_id')) $updateData['conference_id'] = $request->conference_id;
                if ($request->has('title')) $updateData['title'] = $request->title;
                if ($request->has('abstract')) $updateData['abstract'] = $request->abstract;
                if ($request->has('keywords')) $updateData['keywords'] = $request->keywords;
                if ($request->has('track_id')) $updateData['track_id'] = $request->track_id;
                
                if (!empty($updateData)) {
                    DB::table('baibao')
                        ->where('paper_id', $id)
                        ->update($updateData);
                }
                
                DB::commit();
                
                // Get updated paper
                $updatedPaper = DB::table('baibao')
                    ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
                    ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
                    ->where('baibao.paper_id', $id)
                    ->select(
                        'baibao.*',
                        'hoithao.title as conference_title',
                        'trangthaibaibao.status_name'
                    )
                    ->first();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Cập nhật bài báo thành công',
                    'data' => $updatedPaper
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi cập nhật bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw a paper with permission checks
     * POST /api/papers/{id}/withdraw
     */
    public function withdraw(Request $request, $id)
    {
        try {
            $userId = auth()->id();
            
            $validator = Validator::make($request->all(), [
                'reason' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Get paper with conference deadline info
            $paper = DB::table('baibao')
                ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
                ->where('baibao.paper_id', $id)
                ->where('baibao.submitter_id', $userId)
                ->select(
                    'baibao.*',
                    'hoithao.deadline_submission',
                    'hoithao.deadline_camera_ready'
                )
                ->first();
            
            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo hoặc bạn không có quyền rút bài'
                ], 404);
            }
            
            // Check if can withdraw based on deadline and review logic
            $withdrawPermission = $this->canWithdrawPaper($paper);
            if (!$withdrawPermission['can_withdraw']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $withdrawPermission['reason']
                ], 403);
            }
            
            // Update status to WITHDRAWN
            DB::table('baibao')
                ->where('paper_id', $id)
                ->update([
                    'status_code' => 'WITHDRAWN',
                    'withdrawal_reason' => $request->reason ?? null,
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Rút bài báo thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi rút bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete paper (kept for backward compatibility, but recommend using withdraw)
     * DELETE /api/papers/{id}
     */
    public function destroy($id)
    {
        // Redirect to withdraw method
        return $this->withdraw(request(), $id);
    }

    /**
     * Get my papers (as author or submitter) with statistics and permissions
     * GET /api/my-papers
     */
    public function myPapers(Request $request)
    {
        try {
            $userId = auth()->id();

            // Get papers with conference deadline info for permissions
            $query = DB::table('baibao')
                ->join('hoithao', 'baibao.conference_id', '=', 'hoithao.conference_id')
                ->join('trangthaibaibao', 'baibao.status_code', '=', 'trangthaibaibao.status_code')
                ->where('baibao.submitter_id', $userId)
                ->select(
                    'baibao.paper_id',
                    'baibao.title',
                    'baibao.abstract',
                    'baibao.keywords',
                    'baibao.created_at',
                    'baibao.status_code',
                    'baibao.file_path',
                    'hoithao.title as conference_title',
                    'hoithao.conference_id',
                    'hoithao.deadline_submission',
                    'hoithao.deadline_camera_ready',
                    'trangthaibaibao.status_name'
                );

            // Filter by status
            if ($request->has('status')) {
                $query->where('baibao.status_code', $request->status);
            }

            // Filter by conference
            if ($request->has('conference_id')) {
                $query->where('baibao.conference_id', $request->conference_id);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'baibao.created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $papers = $query->paginate($perPage);

            // Add permission checks for each paper
            $papers->getCollection()->transform(function ($paper) {
                $editPermission = $this->canEditPaper($paper);
                $withdrawPermission = $this->canWithdrawPaper($paper);
                
                $paper->can_edit = $editPermission['can_edit'];
                $paper->edit_reason = $editPermission['reason'];
                $paper->can_withdraw = $withdrawPermission['can_withdraw'];
                $paper->withdraw_reason = $withdrawPermission['reason'];
                
                // Format dates for Flutter
                $paper->formatted_created_at = Carbon::parse($paper->created_at)->format('d/m/Y H:i');
                $paper->formatted_deadline = Carbon::parse($paper->deadline_submission)->format('d/m/Y');
                
                return $paper;
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách bài báo của tôi',
                'data' => $papers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get author dashboard statistics
     * GET /api/author/statistics
     */
    public function authorStatistics()
    {
        try {
            $userId = auth()->id();
            
            $stats = [
                'total' => DB::table('baibao')->where('submitter_id', $userId)->count(),
                'draft' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'DRAFT')->count(),
                'submitted' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'SUBMITTED')->count(),
                'under_review' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'UNDER_REVIEW')->count(),
                'accepted' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'ACCEPTED')->count(),
                'rejected' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'REJECTED')->count(),
                'withdrawn' => DB::table('baibao')->where('submitter_id', $userId)->where('status_code', 'WITHDRAWN')->count(),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'Thống kê bài báo của tác giả',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get paper statistics
     * GET /api/papers/statistics
     */
    public function statistics(Request $request)
    {
        try {
            $query = BaiBao::query();

            // Filter by conference
            if ($request->has('conference_id')) {
                $query->where('conference_id', $request->conference_id);
            }

            $stats = [
                'total' => $query->count(),
                'by_status' => [],
                'by_track' => [],
            ];

            // Count by status
            $statusCounts = (clone $query)->select('status_code', DB::raw('count(*) as count'))
                ->groupBy('status_code')
                ->get();

            foreach ($statusCounts as $status) {
                $stats['by_status'][$status->status_code] = $status->count;
            }

            // Count by track
            $trackCounts = (clone $query)->select('track_id', DB::raw('count(*) as count'))
                ->whereNotNull('track_id')
                ->groupBy('track_id')
                ->with('tieuBan:track_id,title')
                ->get();

            foreach ($trackCounts as $track) {
                $stats['by_track'][] = [
                    'track_id' => $track->track_id,
                    'track_name' => $track->tieuBan->title ?? 'N/A',
                    'count' => $track->count
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Thống kê bài báo',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy thống kê: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download paper file
     * GET /api/papers/{id}/download
     */
    public function download($id)
    {
        try {
            $paper = BaiBao::with('currentVersion')->find($id);

            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }

            // Check permissions
            $user = auth()->user();
            $canView = $this->canViewPaper($user, $paper);

            if (!$canView) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền tải bài báo này'
                ], 403);
            }

            if (!$paper->currentVersion || !$paper->currentVersion->file_path) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy file bài báo'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $paper->currentVersion->file_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File không tồn tại'
                ], 404);
            }

            return response()->download($filePath);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi tải file: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method to check if user can view paper
    private function canViewPaper($user, $paper)
    {
        // Admin can view all papers
        if ($this->isAdmin($user)) {
            return true;
        }

        // Submitter can view own paper
        if ($paper->submitter_id === $user->user_id) {
            return true;
        }

        // Co-authors can view paper
        if ($paper->tacGias()->where('user_id', $user->user_id)->exists()) {
            return true;
        }

        // Track chair can view papers in their track
        if ($paper->track_id) {
            $track = TieuBan::find($paper->track_id);
            if ($track && $track->chair_id === $user->user_id) {
                return true;
            }
        }

        // Assigned reviewers can view paper
        if ($paper->phanCongs()->where('reviewer_id', $user->user_id)->exists()) {
            return true;
        }

        return false;
    }

    // Helper method to check if user is admin
    private function isAdmin($user)
    {
        return DB::table('vaitronguoidung')
            ->where('user_id', $user->user_id)
            ->where('role_code', 'ADMIN')
            ->exists();
    }
}




