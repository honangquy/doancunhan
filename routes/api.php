<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConferenceController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\ConferenceRequestController;
use App\Http\Controllers\Api\PaperController;
use App\Http\Controllers\Api\PaperVersionController;
use App\Http\Controllers\Api\BiddingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\COIController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\AdminController;

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

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public conference routes (read-only)
Route::get('conferences', [ConferenceController::class, 'index']);
Route::get('conferences/{id}', [ConferenceController::class, 'show']);
Route::get('conferences/{id}/statistics', [ConferenceController::class, 'statistics']);

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
    Route::get('conference-requests/statistics', [ConferenceRequestController::class, 'statistics']);

    // Paper Management (Phase 4)
    Route::get('papers', [PaperController::class, 'index']);
    Route::post('papers', [PaperController::class, 'store']);
    Route::get('papers/statistics', [PaperController::class, 'statistics']);
    Route::get('papers/{id}', [PaperController::class, 'show']);
    Route::put('papers/{id}', [PaperController::class, 'update']);
    Route::delete('papers/{id}', [PaperController::class, 'destroy']);
    Route::get('papers/{id}/download', [PaperController::class, 'download']);
    Route::get('my-papers', [PaperController::class, 'myPapers']);

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

    // COI Management (Phase 5)
    Route::post('coi/declare', [COIController::class, 'declare']); // Declare COI manually
    Route::get('papers/{paper_id}/coi', [COIController::class, 'paperCOIs']); // List paper COIs (Admin/Chair)
    Route::get('coi', [COIController::class, 'index']); // List all COIs (Admin)
    Route::post('coi/detect', [COIController::class, 'detect']); // Auto-detect COI (Admin)
    Route::post('coi/{coi_id}/resolve', [COIController::class, 'resolve']); // Resolve COI (Chair)
    Route::get('coi/statistics', [COIController::class, 'statistics']); // COI statistics (Admin)

    // Assignment System (Phase 5) - COMPLETE!
    Route::post('assignments', [AssignmentController::class, 'store']); // Manual assignment
    Route::post('assignments/auto-assign', [AssignmentController::class, 'autoAssign']); // Auto-assignment algorithm
    Route::delete('assignments/{assignment_id}', [AssignmentController::class, 'destroy']); // Unassign reviewer
    Route::get('papers/{paper_id}/assignments', [AssignmentController::class, 'paperAssignments']); // Paper assignments
    Route::get('my-assignments', [AssignmentController::class, 'myAssignments']); // My assignments (Reviewer)
    Route::put('assignments/{assignment_id}/accept', [AssignmentController::class, 'acceptAssignment']); // Accept/reject
    Route::get('assignment/statistics', [AssignmentController::class, 'statistics']); // Assignment statistics

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

    // TODO: Notifications (Phase 7)
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'HUIT Conference API is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

