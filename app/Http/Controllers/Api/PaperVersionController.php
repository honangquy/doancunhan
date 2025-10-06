<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaiBao;
use App\Models\Models\PhienBanBaiBao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaperVersionController extends Controller
{
    /**
     * Get all versions of a paper
     * GET /api/papers/{paper_id}/versions
     */
    public function index($paperId)
    {
        try {
            $paper = BaiBao::find($paperId);

            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }

            // Check permissions
            $user = auth()->user();
            if (!$this->canAccessPaper($user, $paper)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền xem các phiên bản'
                ], 403);
            }

            $versions = PhienBanBaiBao::where('paper_id', $paperId)
                ->orderBy('version_no', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Danh sách phiên bản',
                'data' => $versions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy danh sách phiên bản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a new version
     * POST /api/papers/{paper_id}/versions
     */
    public function store(Request $request, $paperId)
    {
        try {
            $paper = BaiBao::with('hoiThao')->find($paperId);

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
                    'message' => 'Chỉ người nộp bài mới có thể upload phiên bản mới'
                ], 403);
            }

            // Check if paper allows new version
            if (!in_array($paper->status_code, ['SUBMITTED', 'REVISION_REQUIRED', 'REVISED'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể upload phiên bản mới ở trạng thái hiện tại'
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
                'note' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Get next version number
            $lastVersion = PhienBanBaiBao::where('paper_id', $paperId)
                ->orderBy('version_no', 'desc')
                ->first();
            $nextVersionNo = $lastVersion ? $lastVersion->version_no + 1 : 1;

            // Upload file
            $file = $request->file('file');
            $fileName = 'paper_' . $paper->paper_id . '_v' . $nextVersionNo . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('papers/' . $paper->conference_id, $fileName, 'public');

            // Create new version
            $version = PhienBanBaiBao::create([
                'paper_id' => $paper->paper_id,
                'version_no' => $nextVersionNo,
                'file_path' => $filePath,
                'submitted_at' => now(),
                'note' => $request->note ?? "Phiên bản {$nextVersionNo}",
            ]);

            // Update current version
            $paper->update(['current_version_id' => $version->version_id]);

            // Update paper status if it was REVISION_REQUIRED
            if ($paper->status_code === 'REVISION_REQUIRED') {
                $paper->update(['status_code' => 'REVISED']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Upload phiên bản mới thành công',
                'data' => $version
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi upload phiên bản mới: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific version
     * GET /api/papers/{paper_id}/versions/{version_no}
     */
    public function show($paperId, $versionNo)
    {
        try {
            $paper = BaiBao::find($paperId);

            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }

            // Check permissions
            $user = auth()->user();
            if (!$this->canAccessPaper($user, $paper)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền xem phiên bản này'
                ], 403);
            }

            $version = PhienBanBaiBao::where('paper_id', $paperId)
                ->where('version_no', $versionNo)
                ->first();

            if (!$version) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy phiên bản'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Chi tiết phiên bản',
                'data' => $version
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi lấy chi tiết phiên bản: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a specific version
     * GET /api/papers/{paper_id}/versions/{version_no}/download
     */
    public function download($paperId, $versionNo)
    {
        try {
            $paper = BaiBao::find($paperId);

            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }

            // Check permissions
            $user = auth()->user();
            if (!$this->canAccessPaper($user, $paper)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền tải phiên bản này'
                ], 403);
            }

            $version = PhienBanBaiBao::where('paper_id', $paperId)
                ->where('version_no', $versionNo)
                ->first();

            if (!$version) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy phiên bản'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $version->file_path);

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

    /**
     * Compare two versions
     * GET /api/papers/{paper_id}/versions/compare
     */
    public function compare(Request $request, $paperId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'version1' => 'required|integer',
                'version2' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $paper = BaiBao::find($paperId);

            if (!$paper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài báo'
                ], 404);
            }

            // Check permissions
            $user = auth()->user();
            if (!$this->canAccessPaper($user, $paper)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có quyền so sánh phiên bản'
                ], 403);
            }

            $version1 = PhienBanBaiBao::where('paper_id', $paperId)
                ->where('version_no', $request->version1)
                ->first();

            $version2 = PhienBanBaiBao::where('paper_id', $paperId)
                ->where('version_no', $request->version2)
                ->first();

            if (!$version1 || !$version2) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Một trong các phiên bản không tồn tại'
                ], 404);
            }

            $comparison = [
                'version1' => $version1,
                'version2' => $version2,
                'time_diff' => $version2->submitted_at->diffForHumans($version1->submitted_at),
                'size_diff' => $this->getFileSizeDiff($version1->file_path, $version2->file_path),
            ];

            return response()->json([
                'status' => 'success',
                'message' => 'So sánh phiên bản',
                'data' => $comparison
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi khi so sánh phiên bản: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method to check if user can access paper
    private function canAccessPaper($user, $paper)
    {
        // Admin can access all papers
        if ($this->isAdmin($user)) {
            return true;
        }

        // Submitter can access own paper
        if ($paper->submitter_id === $user->user_id) {
            return true;
        }

        // Co-authors can access paper
        if ($paper->tacGias()->where('user_id', $user->user_id)->exists()) {
            return true;
        }

        // Track chair can access papers in their track
        if ($paper->track_id) {
            $track = \App\Models\TieuBan::find($paper->track_id);
            if ($track && $track->chair_id === $user->user_id) {
                return true;
            }
        }

        // Assigned reviewers can access paper
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

    // Helper method to get file size difference
    private function getFileSizeDiff($filePath1, $filePath2)
    {
        $path1 = storage_path('app/public/' . $filePath1);
        $path2 = storage_path('app/public/' . $filePath2);

        if (!file_exists($path1) || !file_exists($path2)) {
            return 'N/A';
        }

        $size1 = filesize($path1);
        $size2 = filesize($path2);
        $diff = $size2 - $size1;

        return $this->formatBytes(abs($diff)) . ' (' . ($diff >= 0 ? '+' : '-') . ')';
    }

    // Helper method to format bytes
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
