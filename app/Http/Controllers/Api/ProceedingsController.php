<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HoiThao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Proceedings",
 *     description="API endpoints cho quản lý kỷ yếu hội thảo (Author view)"
 * )
 */
class ProceedingsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/proceedings",
     *     summary="Lấy danh sách hội thảo mà user là AUTHOR có thể xem kỷ yếu",
     *     tags={"Proceedings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Danh sách hội thảo thành công",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="conference_id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="year", type="integer"),
     *                     @OA\Property(property="start_date", type="string", format="date"),
     *                     @OA\Property(property="end_date", type="string", format="date"),
     *                     @OA\Property(property="has_proceedings", type="boolean"),
     *                     @OA\Property(property="proceedings_published_at", type="string", format="datetime", nullable=true),
     *                     @OA\Property(property="paper_count", type="integer"),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        try {
            $userId = auth()->user()->user_id;

            // Lấy danh sách hội thảo mà user có role AUTHOR
            $conferences = HoiThao::whereHas('vaiTroNguoiDungs', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->where('role_code', 'AUTHOR');
            })
            ->withCount(['baiBaos as paper_count' => function ($query) use ($userId) {
                // Đếm số bài mà user là tác giả hoặc submitter
                $query->where(function ($q) use ($userId) {
                    $q->where('submitter_id', $userId)
                      ->orWhereHas('tacGias', function ($tq) use ($userId) {
                          $tq->where('user_id', $userId);
                      });
                });
            }])
            ->select([
                'conference_id',
                'title',
                'year',
                'start_date',
                'end_date',
                'proceedings_file',
                'proceedings_published_at'
            ])
            ->orderBy('year', 'desc')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($conference) {
                return [
                    'conference_id' => $conference->conference_id,
                    'title' => $conference->title,
                    'year' => $conference->year,
                    'start_date' => $conference->start_date,
                    'end_date' => $conference->end_date,
                    'has_proceedings' => !is_null($conference->proceedings_file),
                    'proceedings_published_at' => $conference->proceedings_published_at,
                    'paper_count' => $conference->paper_count ?? 0,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $conferences,
                'message' => 'Lấy danh sách hội thảo thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách hội thảo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/proceedings/{conferenceId}",
     *     summary="Xem chi tiết kỷ yếu của một hội thảo",
     *     tags={"Proceedings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="conferenceId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Thông tin kỷ yếu",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="conference_id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="year", type="integer"),
     *                 @OA\Property(property="has_proceedings", type="boolean"),
     *                 @OA\Property(property="proceedings_url", type="string", nullable=true),
     *                 @OA\Property(property="proceedings_published_at", type="string", nullable=true),
     *                 @OA\Property(property="file_size", type="integer", nullable=true, description="Kích thước file (bytes)"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Không có quyền truy cập"),
     *     @OA\Response(response=404, description="Không tìm thấy hội thảo")
     * )
     */
    public function show($conferenceId)
    {
        try {
            $userId = auth()->user()->user_id;

            // Kiểm tra user có phải AUTHOR của hội thảo này không
            $isAuthor = DB::table('vaitronguoidung')
                ->where('conference_id', $conferenceId)
                ->where('user_id', $userId)
                ->where('role_code', 'AUTHOR')
                ->exists();

            if (!$isAuthor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền xem kỷ yếu của hội thảo này'
                ], 403);
            }

            // Lấy thông tin hội thảo
            $conference = HoiThao::where('conference_id', $conferenceId)
                ->select([
                    'conference_id',
                    'title',
                    'year',
                    'start_date',
                    'end_date',
                    'proceedings_file',
                    'proceedings_published_at'
                ])
                ->first();

            if (!$conference) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy hội thảo'
                ], 404);
            }

            $data = [
                'conference_id' => $conference->conference_id,
                'title' => $conference->title,
                'year' => $conference->year,
                'start_date' => $conference->start_date,
                'end_date' => $conference->end_date,
                'has_proceedings' => !is_null($conference->proceedings_file),
                'proceedings_url' => null,
                'proceedings_published_at' => $conference->proceedings_published_at,
                'file_size' => null,
            ];

            // Nếu có file kỷ yếu, thêm URL và thông tin file
            if ($conference->proceedings_file) {
                // Tạo URL public để mobile app có thể truy cập
                $data['proceedings_url'] = asset('storage/' . $conference->proceedings_file);

                // Lấy kích thước file nếu tồn tại
                if (Storage::disk('public')->exists($conference->proceedings_file)) {
                    $data['file_size'] = Storage::disk('public')->size($conference->proceedings_file);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Lấy thông tin kỷ yếu thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin kỷ yếu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/proceedings/{conferenceId}/download",
     *     summary="Tải xuống file kỷ yếu (trả về file trực tiếp)",
     *     tags={"Proceedings"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="conferenceId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File PDF",
     *         @OA\MediaType(mediaType="application/pdf")
     *     ),
     *     @OA\Response(response=403, description="Không có quyền truy cập"),
     *     @OA\Response(response=404, description="Không tìm thấy file")
     * )
     */
    public function download($conferenceId)
    {
        try {
            $userId = auth()->user()->user_id;

            // Kiểm tra quyền truy cập
            $isAuthor = DB::table('vaitronguoidung')
                ->where('conference_id', $conferenceId)
                ->where('user_id', $userId)
                ->where('role_code', 'AUTHOR')
                ->exists();

            if (!$isAuthor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền tải kỷ yếu của hội thảo này'
                ], 403);
            }

            // Lấy thông tin file
            $conference = HoiThao::where('conference_id', $conferenceId)
                ->select(['conference_id', 'title', 'year', 'proceedings_file'])
                ->first();

            if (!$conference || !$conference->proceedings_file) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kỷ yếu chưa được xuất bản'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $conference->proceedings_file);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy file kỷ yếu'
                ], 404);
            }

            // Trả về file để download
            return response()->download(
                $filePath,
                "Ky_Yeu_{$conference->title}_{$conference->year}.pdf",
                [
                    'Content-Type' => 'application/pdf',
                ]
            );

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tải file: ' . $e->getMessage()
            ], 500);
        }
    }
}
