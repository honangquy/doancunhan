<?php

namespace App\Http\Controllers;

use App\Models\YeuCauHoiThao;
use App\Models\ThemVienBoSung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConferenceRequestController extends Controller
{
    /**
     * Store a new conference request
     */
    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            // User must be verified
            if (!$user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email verification required to submit conference requests.',
                ], 403);
            }

            // Dynamic validation rules
            $rules = [
                'title' => 'required|string|max:255',
                'field' => 'required|string|max:255',
                'level_code' => 'required|in:KHOA,TRUONG',
                'expected_date' => 'required|date|after_or_equal:today',
                'objective' => 'required|string|max:500',
                'affiliation' => 'nullable|string|max:255',
                'chair_fullname' => 'required|string|max:255',
                'chair_email' => 'required|email|max:255',
                'chair_phone' => 'nullable|string|max:20',
                'proposal_file' => 'required|file|mimes:pdf|max:10240', // 10MB
                'co_chairs' => 'nullable|json',
            ];

            // Add faculty_name validation for KHOA level
            if ($request->level_code === 'KHOA') {
                $rules['faculty_name'] = 'required|string|max:255';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Simple duplicate submission protection (server-side):
            // If the same user has created a request with the same title, expected_date and level_code
            // within the last 2 minutes, treat it as a duplicate and reject with 409.
            $duplicateWindowMinutes = 2;
            $recentDuplicate = YeuCauHoiThao::where('user_id', $user->id)
                ->where('title', $request->title)
                ->where('expected_date', $request->expected_date)
                ->where('level_code', $request->level_code)
                ->where('created_at', '>=', now()->subMinutes($duplicateWindowMinutes))
                ->first();

            if ($recentDuplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có vẻ như bạn vừa gửi yêu cầu giống vậy. Vui lòng kiểm tra danh sách yêu cầu của bạn trước khi gửi lại.',
                ], 409);
            }

            DB::beginTransaction();

            try {
                // Store proposal file after duplicate check to avoid storing duplicates
                $filePath = $request->file('proposal_file')->store('conference-requests', 'public');

                // Create conference request
                $conferenceRequest = YeuCauHoiThao::create([
                    'user_id' => $user->id,
                    'title' => $request->title,
                    'field' => $request->field,
                    'level_code' => $request->level_code,
                    'expected_date' => $request->expected_date,
                    'objective' => $request->objective,
                    'proposal_file' => $filePath,
                    'status' => 'PENDING',
                    'faculty_name' => $request->faculty_name,
                    'affiliation' => $request->affiliation,
                    'chair_fullname' => $request->chair_fullname,
                    'chair_email' => $request->chair_email,
                    'chair_phone' => $request->chair_phone,
                    'created_at' => now(),
                ]);

                // Parse and store co-chairs
                if ($request->has('co_chairs') && $request->co_chairs) {
                    $coChairs = json_decode($request->co_chairs, true);
                    if (is_array($coChairs)) {
                        foreach ($coChairs as $coChair) {
                            if (!empty($coChair['fullname']) && !empty($coChair['email'])) {
                                ThemVienBoSung::create([
                                    'request_id' => $conferenceRequest->request_id,
                                    'fullname' => $coChair['fullname'],
                                    'email' => $coChair['email'],
                                    'affiliation' => $coChair['affiliation'] ?? null,
                                ]);
                            }
                        }
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Yêu cầu tạo hội thảo đã được gửi thành công!',
                    'request_id' => $conferenceRequest->request_id,
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                
                // Delete uploaded file if database operation failed
                if (isset($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Database error: ' . $e->getMessage(),
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}