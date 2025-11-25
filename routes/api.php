<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\ConferenceRequestController;
use App\Http\Controllers\Api\PaperController;
use App\Http\Controllers\Api\PaperVersionController;
use App\Http\Controllers\Api\BiddingController;
use App\Http\Controllers\Api\ReviewController;
// use App\Http\Controllers\Api\COIController; // TODO: Create this controller
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\NewsController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', function () {
    $routeCollection = Illuminate\Support\Facades\Route::getRoutes();
    $routes = [];

    foreach ($routeCollection as $route) {
        if (str_starts_with($route->uri(), 'api/') && $route->uri() !== 'api') {
            $routes[] = [
                'method' => implode('|', $route->methods()),
                'uri' => '/' . $route->uri(),
                'action' => $route->getActionName(),
            ];
        }
    }
    return response()->json($routes);
});

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public conference routes (read-only)
Route::get('conferences', [ConferenceController::class, 'index']);
Route::get('conferences/{id}', [ConferenceController::class, 'show']);
Route::get('conferences/{id}/statistics', [ConferenceController::class, 'statistics']);

// Public facilities endpoint (for dropdown in forms)
Route::get('facilities', function () {
    $facilities = \App\Models\Khoa::select('faculty_id as id', 'faculty_name as name')->get();
    return response()->json(['facilities' => $facilities]);
});

// Protected routes
Route::middleware(['auth:api'])->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    // News Management (Chair Only) - DEBUG: Remove role middleware first
    Route::prefix('news')->group(function () {
        Route::get('/', [NewsController::class, 'index']);
        Route::post('/', [NewsController::class, 'store']);
        Route::get('/{id}', [NewsController::class, 'show']);
        Route::post('/{id}', [NewsController::class, 'update']); // Use POST for file upload
        Route::delete('/{id}', [NewsController::class, 'destroy']);
    });

    // Test endpoint for debugging
    Route::get('test/news', function() {
        $user = auth()->user();
        return response()->json([
            'success' => true,
            'user_id' => $user ? $user->user_id : null,
            'email' => $user ? $user->email : null,
            'has_chair_role' => $user ? $user->hasRole('CHAIR') : false,
            'message' => 'News routes are working!'
        ]);
    });

    // Announcement Management (Chair + User)
    Route::prefix('announcements')->group(function () {
        Route::get('/', [AnnouncementController::class, 'index']); // List announcements
        Route::post('/', [AnnouncementController::class, 'store']); // Create announcement (Chair)
        Route::get('/conferences/list', [AnnouncementController::class, 'getConferences']); // Conference list for Chair
        Route::post('/preview-recipients', [AnnouncementController::class, 'previewRecipients']); // Preview recipient count
        Route::get('/{id}', [AnnouncementController::class, 'show']); // Announcement detail
        Route::put('/{id}', [AnnouncementController::class, 'update']); // Update announcement (Chair)
        Route::delete('/{id}', [AnnouncementController::class, 'destroy']); // Delete announcement (Chair)
        Route::post('/{id}/mark-read', [AnnouncementController::class, 'markAsRead']); // Mark as read (User)
    });

    // Conference Management
    Route::post('conferences', [ConferenceController::class, 'store']);
    Route::put('conferences/{id}', [ConferenceController::class, 'update']);
    Route::delete('conferences/{id}', [ConferenceController::class, 'destroy']);
    Route::get('my-conferences', [ConferenceController::class, 'myConferences']);

    // Track Management
    Route::get('conferences/{conference_id}/tracks', [TrackController::class, 'index']);
    Route::post('conferences/{conference_id}/tracks', [TrackController::class, 'store']);
    Route::get('tracks/{id}', [TrackController::class, 'show']);
    Route::put('tracks/{id}', [TrackController::class, 'update']);
    Route::delete('tracks/{id}', [TrackController::class, 'destroy']);
    Route::get('tracks/{id}/papers', [TrackController::class, 'papers']);
    Route::get('my-tracks', [TrackController::class, 'myTracks']);

    // Conference Request Management
    Route::get('conference-requests', [ConferenceRequestController::class, 'index']);
    Route::post('conference-requests', [ConferenceRequestController::class, 'store']);
    Route::get('conference-requests/{id}', [ConferenceRequestController::class, 'show']);
    Route::post('conference-requests/{id}/approve', [ConferenceRequestController::class, 'approve']);
    Route::post('conference-requests/{id}/reject', [ConferenceRequestController::class, 'reject']);
    Route::post('conference-requests/{id}/cancel', [ConferenceRequestController::class, 'cancel']);
    Route::put('conference-requests/{id}/configure', [ConferenceRequestController::class, 'configure']);
    Route::get('conference-requests/statistics', [ConferenceRequestController::class, 'statistics']);

    // Paper Management (Phase 4)
    Route::get('papers', [PaperController::class, 'index']);
    Route::post('papers', [PaperController::class, 'store']);
    Route::get('papers/statistics', [PaperController::class, 'statistics']);
    Route::get('papers/{id}', [PaperController::class, 'show']);
    Route::put('papers/{id}', [PaperController::class, 'update']);
    Route::delete('papers/{id}', [PaperController::class, 'destroy']);
    Route::post('papers/{id}/withdraw', [PaperController::class, 'withdraw']);
    Route::get('papers/{id}/download', [PaperController::class, 'download']);
    Route::get('my-papers', [PaperController::class, 'myPapers']);
    Route::get('author/statistics', [PaperController::class, 'authorStatistics']);

    // Paper Version Management
    Route::get('papers/{paper_id}/versions', [PaperVersionController::class, 'index']);
    Route::post('papers/{paper_id}/versions', [PaperVersionController::class, 'store']);
    Route::get('papers/{paper_id}/versions/{version_no}', [PaperVersionController::class, 'show']);
    Route::get('papers/{paper_id}/versions/{version_no}/download', [PaperVersionController::class, 'download']);
    Route::get('papers/{paper_id}/versions/compare', [PaperVersionController::class, 'compare']);

    // Bidding System (Phase 5)
    Route::get('papers/{paper_id}/biddings', [BiddingController::class, 'index']); // Admin/Chair view all biddings
    Route::post('papers/{paper_id}/bid', [BiddingController::class, 'store']); // Reviewer bids on paper
    Route::get('my-biddings', [BiddingController::class, 'myBiddings']); // Reviewer's biddings
    Route::put('biddings/{paper_id}', [BiddingController::class, 'update']); // Update bid
    Route::delete('biddings/{paper_id}', [BiddingController::class, 'destroy']); // Withdraw bid
    Route::get('bidding/statistics', [BiddingController::class, 'statistics']); // Admin statistics

    // Review System (Phase 5)
    Route::post('reviews', [ReviewController::class, 'store']); // Submit review
    Route::get('papers/{paper_id}/reviews', [ReviewController::class, 'index']); // View paper reviews (Admin/Chair)
    Route::get('reviews/{review_id}', [ReviewController::class, 'show']); // Review details
    Route::put('reviews/{review_id}', [ReviewController::class, 'update']); // Update review
    Route::get('my-reviews', [ReviewController::class, 'myReviews']); // Reviewer's reviews
    Route::post('reviews/{review_id}/finalize', [ReviewController::class, 'finalize']); // Finalize review
    Route::get('review/statistics', [ReviewController::class, 'statistics']); // Review statistics (Admin)

    // COI Management (Phase 5) - TODO: Implement COIController
    // Route::post('coi/declare', [COIController::class, 'declare']); // Declare COI manually
    // Route::get('papers/{paper_id}/coi', [COIController::class, 'paperCOIs']); // List paper COIs (Admin/Chair)
    // Route::get('coi', [COIController::class, 'index']); // List all COIs (Admin)
    // Route::post('coi/detect', [COIController::class, 'detect']); // Auto-detect COI (Admin)
    // Route::post('coi/{coi_id}/resolve', [COIController::class, 'resolve']); // Resolve COI (Chair)
    // Route::get('coi/statistics', [COIController::class, 'statistics']); // COI statistics (Admin)

    // Assignment System (Phase 5) - COMPLETE!
    Route::post('assignments', [AssignmentController::class, 'store']); // Manual assignment
    Route::post('assignments/auto-assign', [AssignmentController::class, 'autoAssign']); // Auto-assignment algorithm
    Route::delete('assignments/{assignment_id}', [AssignmentController::class, 'destroy']); // Unassign reviewer
    Route::get('papers/{paper_id}/assignments', [AssignmentController::class, 'paperAssignments']); // Paper assignments
    Route::get('my-assignments', [AssignmentController::class, 'myAssignments']); // My assignments (Reviewer)
    Route::put('assignments/{assignment_id}/accept', [AssignmentController::class, 'acceptAssignment']); // Accept/reject
    Route::get('assignment/statistics', [AssignmentController::class, 'statistics']); // Assignment statistics

    // Reviewer Revision Tracking (Mobile App Integration)
    Route::prefix('reviewer')->group(function () {
        Route::get('papers-with-revisions', [\App\Http\Controllers\Api\ReviewerRevisionController::class, 'getPapersWithRevisions']); // Danh sách bài có revision
        Route::get('papers/{paper_id}/revision-history', [\App\Http\Controllers\Api\ReviewerRevisionController::class, 'getRevisionHistory']); // Lịch sử revision
        Route::post('papers/{paper_id}/confirm-revision', [\App\Http\Controllers\Api\ReviewerRevisionController::class, 'confirmRevision']); // Xác nhận revision
        Route::get('papers/{paper_id}/compare-versions', [\App\Http\Controllers\Api\ReviewerRevisionController::class, 'compareVersions']); // So sánh versions
    });

    // Admin & Reports (Phase 6) - 100% COMPLETE! 🎉
    Route::prefix('admin')->group(function () {
        // User Management (3 APIs)
        Route::get('users', [AdminController::class, 'listUsers']); // List all users
        Route::put('users/{id}', [AdminController::class, 'updateUser']); // Update user, lock/unlock
        Route::post('users/{id}/roles', [AdminController::class, 'manageRoles']); // Assign/revoke roles

        // System Reports (2 APIs)
        Route::get('reports/conference/{id}', [AdminController::class, 'conferenceReport']); // Conference report
        Route::get('reports/overview', [AdminController::class, 'systemOverview']); // System overview
    });

    // Chair Management APIs for Flutter (NEW)
    Route::prefix('chair')->group(function () {
        // Dashboard & Overview
        Route::get('dashboard', [\App\Http\Controllers\Api\ChairController::class, 'dashboard']); // Dashboard statistics

        // Paper Management
        Route::get('papers', [\App\Http\Controllers\Api\ChairController::class, 'papers']); // List all papers with filters
        Route::get('papers/{id}', [\App\Http\Controllers\Api\ChairController::class, 'showPaper']); // Paper detail with reviews

        // Reviewer Assignment
        Route::get('papers/{id}/available-reviewers', [\App\Http\Controllers\Api\ChairController::class, 'getAvailableReviewers']); // Get available reviewers
        Route::post('papers/{id}/assign-reviewer', [\App\Http\Controllers\Api\ChairController::class, 'assignReviewer']); // Assign reviewer
        Route::delete('assignments/{id}', [\App\Http\Controllers\Api\ChairController::class, 'removeAssignment']); // Remove assignment

        // Decision Making
        Route::post('papers/{id}/decision', [\App\Http\Controllers\Api\ChairController::class, 'makeDecision']); // Accept/Reject paper

        // Statistics & Reports
        Route::get('conferences/{id}/review-statistics', [\App\Http\Controllers\Api\ChairController::class, 'reviewStatistics']); // Review statistics
        Route::get('reviewers', [\App\Http\Controllers\Api\ChairController::class, 'listReviewers']); // List reviewers with performance
    });

    // Notifications (Phase 7)
    Route::get('notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('notifications/unread', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
    Route::get('notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'show']);
    Route::patch('notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);
    Route::patch('notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy']);
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'HUIT Conference API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Test route without CSRF for debugging
Route::post('test-conference-submit', function (Request $request) {
    Log::info('API Test route called', [
        'method' => $request->method(),
        'data' => $request->all()
    ]);

    return response()->json([
        'success' => true,
        'message' => 'API route works without CSRF',
        'data' => $request->all(),
        'received_files' => $request->hasFile('proposal_file') ? 'Yes' : 'No'
    ]);
});

// Real conference request endpoint with proper validation
Route::post('submit-conference-request', function (Request $request) {
    try {
        Log::info('Conference request API called', $request->all());
        // Validate request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'objective' => 'required|string',
            'level_code' => 'required|in:KHOA,TRUONG',
            'chair_fullname' => 'required|string|max:255',
            'chair_email' => 'required|email|max:255',
            'proposal_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle file upload
        $fileName = null;
        if ($request->hasFile('proposal_file')) {
            $file = $request->file('proposal_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('conference-requests', $fileName, 'public');
        }

        // Create conference request record
        $conferenceRequest = \App\Models\YeuCauHoiThao::create([
            'title' => $request->title,
            'objective' => $request->objective, // Changed from description
            'level_code' => $request->level_code,
            'faculty_name' => $request->faculty_name,
            'affiliation' => $request->affiliation,
            'chair_fullname' => $request->chair_fullname,
            'chair_email' => $request->chair_email,
            'chair_phone' => $request->chair_phone,
            'proposal_file' => $fileName, // Store filename only
            'status' => 'PENDING',
            'created_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conference request submitted successfully',
            'request_id' => $conferenceRequest->request_id,
        ]);

    } catch (\Exception $e) {
        Log::error('Conference request error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'An error occurred while processing your request',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Test routes for auto assignment
Route::prefix('test')->group(function () {
    Route::get('stats', function () {
        return [
            'papers' => DB::table('baibao')->count(),
            'biddings' => DB::table('reviewer_bidding')->count(),
            'assignments' => DB::table('reviewer_assignments')->count(),
            'unassigned' => DB::table('baibao as b')
                ->leftJoin('reviewer_assignments as ra', 'b.paper_id', '=', 'ra.paper_id')
                ->whereNull('ra.paper_id')
                ->count()
        ];
    });

    Route::get('papers', function () {
        return DB::table('baibao as b')
            ->select([
                'b.paper_id',
                'b.title',
                DB::raw('(SELECT COUNT(*) FROM reviewer_bidding WHERE paper_id = b.paper_id) as bidding_count'),
                DB::raw('(SELECT COUNT(*) FROM reviewer_assignments WHERE paper_id = b.paper_id) as assignment_count')
            ])
            ->get();
    });
});

// === Chair Assignment Management API ===
Route::prefix('chair')->middleware(['auth:api'])->group(function () {
    Route::prefix('assignments')->group(function () {
        // Dashboard data
        Route::get('conferences/{id}/assignments', [\App\Http\Controllers\Chair\AssignmentController::class, 'index']);

        // Paper bidding details
        Route::get('papers/{id}/bidding', [\App\Http\Controllers\Chair\AssignmentController::class, 'showPaperBidding']);

        // Assign reviewers
        Route::post('papers/{id}/assign', [\App\Http\Controllers\Chair\AssignmentController::class, 'assignReviewers']);

        // Unassign reviewer
        Route::delete('papers/{paperId}/reviewers/{reviewerId}', [\App\Http\Controllers\Chair\AssignmentController::class, 'unassignReviewer']);

        // Auto-assign
        Route::post('conferences/{id}/auto-assign', [\App\Http\Controllers\Chair\AssignmentController::class, 'autoAssignConference']);

        // Statistics
        Route::get('conferences/{id}/stats', [\App\Http\Controllers\Chair\AssignmentController::class, 'getAssignmentStats']);
    });
});

// === REVIEWER MOBILE APP API ===
Route::prefix('mobile/reviewer')->middleware(['auth:api'])->group(function () {
    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'getDashboard']);

    // Assignments
    Route::get('assignments', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'getAssignments']);
    Route::get('assignments/{id}', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'getAssignmentDetail']);
    Route::post('assignments/{id}/accept', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'acceptAssignment']);
    Route::post('assignments/{id}/decline', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'declineAssignment']);

    // Papers
    Route::get('papers/{paper_id}/versions', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'getPaperVersions']);

    // Reviews
    Route::get('reviews', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'getReviews']);
    Route::get('reviews/{id}', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'getReviewDetail']);
    Route::post('reviews', [\App\Http\Controllers\Api\ReviewerMobileController::class, 'submitReview']);
});
