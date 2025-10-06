<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiBao;
use App\Models\HoiThao;
use App\Models\TieuBan;
use App\Models\Models\PhienBanBaiBao;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PaperController extends Controller
{
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
                'conference_id' => 'required|integer|exists:HoiThao,conference_id',
                'track_id' => 'nullable|integer|exists:TieuBan,track_id',
                'title' => 'required|string|max:500',
                'abstract' => 'required|string',
                'authors' => 'required|array|min:1',
                'authors.*.user_id' => 'nullable|integer|exists:NguoiDung,user_id',
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
                    DB::table('TacGiaBaiBao')->insert([
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

                    DB::table('TacGiaBaiBao')->insert([
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
     * Display the specified paper
     * GET /api/papers/{id}
     */
    public function show($id)
    {
        try {
            $paper = BaiBao::with([
                'hoiThao',
                'tieuBan',
                'submitter',
                'tacGias',
                'phienBans',
                'currentVersion',
                'lichSuTrangThais.changedBy'
            ])->find($id);

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
                    'message' => 'Không có quyền xem bài báo này'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Chi tiết bài báo',
                'data' => $paper
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy chi tiết bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified paper
     * PUT /api/papers/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $paper = BaiBao::find($id);

            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }

            // Check permissions
            $user = auth()->user();
            if ($paper->submitter_id !== $user->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chỉ người nộp bài mới có thể chỉnh sửa'
                ], 403);
            }

            // Check if paper can be edited
            if (!in_array($paper->status_code, ['SUBMITTED', 'REVISION_REQUIRED'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể chỉnh sửa bài báo ở trạng thái hiện tại'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|string|max:500',
                'abstract' => 'sometimes|string',
                'track_id' => 'nullable|integer|exists:TieuBan,track_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check track belongs to conference
            if ($request->has('track_id') && $request->track_id) {
                $track = TieuBan::where('track_id', $request->track_id)
                    ->where('conference_id', $paper->conference_id)
                    ->first();
                
                if (!$track) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Track không thuộc hội thảo này'
                    ], 400);
                }
            }

            $paper->update($request->only(['title', 'abstract', 'track_id']));

            $paper->load([
                'hoiThao',
                'tieuBan',
                'submitter',
                'tacGias',
                'currentVersion'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật bài báo thành công',
                'data' => $paper
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi cập nhật bài báo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Withdraw a paper
     * DELETE /api/papers/{id}
     */
    public function destroy($id)
    {
        try {
            $paper = BaiBao::find($id);

            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }

            // Check permissions
            $user = auth()->user();
            if ($paper->submitter_id !== $user->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Chỉ người nộp bài mới có thể rút bài'
                ], 403);
            }

            // Check if paper can be withdrawn
            if (in_array($paper->status_code, ['ACCEPTED', 'CAMERA_READY', 'WITHDRAWN'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể rút bài ở trạng thái hiện tại'
                ], 400);
            }

            // Update status to WITHDRAWN
            $paper->update(['status_code' => 'WITHDRAWN']);

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
     * Get my papers (as author or submitter)
     * GET /api/my-papers
     */
    public function myPapers(Request $request)
    {
        try {
            $userId = auth()->id();

            $query = BaiBao::with([
                'hoiThao:conference_id,title,year',
                'tieuBan:track_id,title',
                'submitter:user_id,full_name',
                'tacGias:user_id,full_name',
                'currentVersion:version_id,version_no,submitted_at'
            ])->where(function($q) use ($userId) {
                $q->where('submitter_id', $userId)
                  ->orWhereHas('tacGias', function($q2) use ($userId) {
                      $q2->where('user_id', $userId);
                  });
            });

            // Filter by status
            if ($request->has('status')) {
                $query->where('status_code', $request->status);
            }

            // Filter by conference
            if ($request->has('conference_id')) {
                $query->where('conference_id', $request->conference_id);
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
        return DB::table('VaiTroNguoiDung')
            ->where('user_id', $user->user_id)
            ->where('role_code', 'ADMIN')
            ->exists();
    }
}
