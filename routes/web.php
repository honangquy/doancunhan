<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Author\PaperController as AuthorPaperController;
use App\Http\Controllers\Reviewer\BiddingController;
use App\Http\Controllers\Reviewer\AssignmentController;
use App\Http\Controllers\Chair\ConferenceController as ChairConferenceController;
use App\Http\Controllers\Chair\PaperController as ChairPaperController;
use App\Http\Controllers\Chair\ReviewerController as ChairReviewerController;
use App\Http\Controllers\Chair\COIController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ConferenceController as AdminConferenceController;
use App\Http\Controllers\Admin\ConferenceRequestController as AdminConferenceRequestController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\ConferenceRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// AJAX Routes for Homepage
Route::get('/api/search-conferences', [HomeController::class, 'searchConferences'])->name('api.search.conferences');
Route::get('/api/conference-counts', [HomeController::class, 'getConferenceCounts'])->name('api.conference.counts');

// Notification Routes
Route::get('/api/notifications', [HomeController::class, 'getNotifications'])->name('api.notifications');
Route::patch('/api/notifications/{id}/read', [HomeController::class, 'markNotificationAsRead'])->name('api.notifications.read');
Route::patch('/api/notifications/read-all', [HomeController::class, 'markAllNotificationsAsRead'])->name('api.notifications.read_all');
Route::post('/api/notifications/sample', [HomeController::class, 'createSampleNotifications'])->name('api.notifications.sample');
// Conference Routes (Public)
Route::get('/conferences', [\App\Http\Controllers\ConferenceController::class, 'index'])->name('conferences.index');
Route::get('/conferences/{id}', [\App\Http\Controllers\ConferenceController::class, 'show'])->name('conferences.show');

// Conference Join Request Routes (Authenticated and Verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/conferences/{id}/join-request', [\App\Http\Controllers\ConferenceController::class, 'submitJoinRequest'])->name('conferences.join-request');
    Route::get('/conferences/{id}/my-requests', [\App\Http\Controllers\ConferenceController::class, 'getUserJoinRequests'])->name('conferences.my-requests');
    Route::get('/my-join-requests', [\App\Http\Controllers\ConferenceController::class, 'myJoinRequests'])->name('join-requests.index');
});

Route::get('/news', [HomeController::class, 'news'])->name('news.index');
Route::get('/process', [HomeController::class, 'process'])->name('process');
Route::get('/support', [HomeController::class, 'support'])->name('support');

// Debug route (no auth required)
Route::get('/debug-status', function () {
    $user = auth()->user();
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => $user ? [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
        ] : null,
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId()
    ]);
});

// Conference Request Routes (Authenticated and Verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/create-conference', function () {
        return view('conference-request.create');
    })->name('conference-request.create');
    
    // Debug route to check user status
    Route::get('/debug-user', function () {
        $user = auth()->user();
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ] : null,
            'csrf_token' => csrf_token()
        ]);
    });
    
    Route::post('/conference-requests', [ConferenceRequestController::class, 'store'])->name('conference-request.store');
    
    // Conference Management Routes
    Route::prefix('conference-management')->name('conference-management.')->group(function () {
        Route::get('/requests', [\App\Http\Controllers\ConferenceManagementController::class, 'requests'])->name('requests');
        Route::get('/requests/{id}', [\App\Http\Controllers\ConferenceManagementController::class, 'showRequest'])->name('request.show');
        Route::post('/requests/{id}/approve', [\App\Http\Controllers\ConferenceManagementController::class, 'approveRequest'])->name('request.approve');
        Route::post('/requests/{id}/reject', [\App\Http\Controllers\ConferenceManagementController::class, 'rejectRequest'])->name('request.reject');
    });
});

// Test Routes (for development)
Route::get('/test-join-requests', function () {
    return view('test-join-requests');
})->name('test.join-requests');

// Test conference request without verified middleware
Route::middleware(['auth'])->group(function () {
    Route::post('/test-conference-requests', [ConferenceRequestController::class, 'store'])->name('test-conference-request.store');
    Route::get('/test-auth-status', function () {
        $user = auth()->user();
        return response()->json([
            'authenticated' => auth()->check(),
            'user_id' => $user ? $user->user_id : null,
            'name' => $user ? $user->name : null,
            'email' => $user ? $user->email : null,
            'email_verified_at' => $user ? $user->email_verified_at : null,
            'has_verified_email' => $user ? $user->hasVerifiedEmail() : false,
            'csrf_token' => csrf_token()
        ]);
    });
});

// Test route completely without authentication
Route::post('/test-no-auth-conference', function (Request $request) {
    \Log::info('Test route called', [
        'method' => $request->method(),
        'headers' => $request->headers->all(),
        'data' => $request->all()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Route works without auth',
        'data' => $request->all(),
        'csrf_token' => csrf_token()
    ]);
});

Route::get('/test-simple', function () {
    return response()->json(['message' => 'Simple test route works']);
});

Route::get('/test-form', function () {
    return view('test-form');
});

// Conference request endpoint without CSRF for testing
Route::post('/submit-conference-request', function (\Illuminate\Http\Request $request) {
    try {
        \Log::info('Web Conference request called', $request->all());
        
        // Validate request
        $validator = \Validator::make($request->all(), [
            'title' => 'required|string|max:500',
            'objective' => 'required|string',
            'level_code' => 'required|in:KHOA,TRUONG',
            'chair_fullname' => 'required|string|max:255',
            'chair_email' => 'required|email|max:255',
            'proposal_file' => 'required|file|mimes:pdf|max:10240',
        ]);
        
        if ($validator->fails()) {
            \Log::error('Validation failed', $validator->errors()->toArray());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        \Log::info('Validation passed, handling file upload');
        
        // Handle file upload
        $fileName = null;
        if ($request->hasFile('proposal_file')) {
            \Log::info('File upload detected');
            $file = $request->file('proposal_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('conference-requests', $fileName, 'public');
            \Log::info('File stored: ' . $fileName);
        }
        
        \Log::info('Creating database record');
        
        // Get authenticated user ID or set to null if not authenticated
        $userId = auth()->check() ? auth()->user()->user_id : null;
        
        // Create conference request record
        $conferenceRequest = \App\Models\YeuCauHoiThao::create([
            'user_id' => $userId,
            'title' => $request->title,
            'objective' => $request->objective,
            'field' => $request->field,
            'level_code' => $request->level_code,
            'faculty_name' => $request->faculty_name,
            'expected_date' => $request->expected_date,
            'affiliation' => $request->affiliation,
            'chair_fullname' => $request->chair_fullname,
            'chair_email' => $request->chair_email,
            'chair_phone' => $request->chair_phone,
            'proposal_file' => $fileName,
            'status' => 'PENDING',
            'created_at' => now(),
        ]);
        
        \Log::info('Record created with ID: ' . $conferenceRequest->request_id);
        
        return response()->json([
            'success' => true,
            'message' => 'Conference request submitted successfully',
            'request_id' => $conferenceRequest->request_id,
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Conference request error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while processing your request',
            'error' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
});

// Guest Routes (not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Email Verification Routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerifyEmailForm'])->name('verification.notice');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->middleware(['throttle:6,1'])->name('verification.send');
});

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');

// Authenticated Routes (require email verification)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [AuthController::class, 'updateAvatar'])->name('profile.avatar');
    
    // Dashboard (role-based)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Author Routes
    Route::prefix('author')->middleware('role:AUTHOR')->name('author.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'authorDashboard'])->name('dashboard');
        
        // Paper Management
        Route::get('/papers', [AuthorPaperController::class, 'index'])->name('papers.index');
        Route::get('/papers/create', [AuthorPaperController::class, 'create'])->name('papers.create');
        Route::post('/papers', [AuthorPaperController::class, 'store'])->name('papers.store');
        Route::get('/papers/{id}', [AuthorPaperController::class, 'show'])->name('papers.show');
        Route::get('/papers/{id}/edit', [AuthorPaperController::class, 'edit'])->name('papers.edit');
        Route::put('/papers/{id}', [AuthorPaperController::class, 'update'])->name('papers.update');
        Route::post('/papers/{id}/withdraw', [AuthorPaperController::class, 'withdraw'])->name('papers.withdraw');
        Route::get('/papers/{id}/download', [AuthorPaperController::class, 'download'])->name('papers.download');
    });
    
    // Reviewer Routes
    Route::prefix('reviewer')->middleware('role:REVIEWER')->name('reviewer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'reviewerDashboard'])->name('dashboard');
        
        // Review Assignments
        Route::get('/assignments', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'assignments'])->name('assignments');
        Route::post('/assignments/{id}/accept', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'acceptAssignment'])->name('assignments.accept');
        Route::post('/assignments/{id}/decline', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'declineAssignment'])->name('assignments.decline');
        
        // Reviews
        Route::get('/reviews', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'reviews'])->name('reviews');
        Route::get('/reviews/create/{assignmentId}', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'createReview'])->name('reviews.create');
        Route::post('/reviews', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'storeReview'])->name('reviews.store');
        Route::get('/reviews/{id}', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'showReview'])->name('reviews.show');
        Route::get('/reviews/{id}/edit', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'editReview'])->name('reviews.edit');
        Route::put('/reviews/{id}', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'updateReview'])->name('reviews.update');
        
        // Paper Download
        Route::get('/papers/{assignmentId}/download', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'downloadPaper'])->name('papers.download');
        
        // Phase 8.10: COI Management
        Route::get('/coi', [\App\Http\Controllers\Reviewer\COIController::class, 'index'])->name('coi.index');
        Route::get('/coi/create', [\App\Http\Controllers\Reviewer\COIController::class, 'create'])->name('coi.create');
        Route::post('/coi', [\App\Http\Controllers\Reviewer\COIController::class, 'store'])->name('coi.store');
        Route::get('/coi/{id}', [\App\Http\Controllers\Reviewer\COIController::class, 'show'])->name('coi.show');
        Route::delete('/coi/{id}', [\App\Http\Controllers\Reviewer\COIController::class, 'retract'])->name('coi.retract');
        Route::get('/coi/search-papers', [\App\Http\Controllers\Reviewer\COIController::class, 'searchPapers'])->name('coi.search-papers');
    });
    
    // Chair Routes
    Route::prefix('chair')->middleware('role:CHAIR')->name('chair.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Chair\ChairController::class, 'dashboard'])->name('dashboard');
        
        // Test route
        Route::get('/test-layout', function () {
            return view('test-chair-layout');
        })->name('test-layout');
        
        // Conference Management (New)
        Route::get('/conferences', [\App\Http\Controllers\Chair\ConferenceSetupController::class, 'index'])->name('conferences.index');
        Route::get('/conferences/configure/{requestId}', [\App\Http\Controllers\Chair\ConferenceSetupController::class, 'configure'])->name('conferences.configure');
        Route::post('/conferences/configure/{requestId}', [\App\Http\Controllers\Chair\ConferenceSetupController::class, 'store'])->name('conferences.store');
        
        // Test route for debugging
        Route::post('/test-conference-debug/{requestId}', function(Request $request, $requestId) {
            \Log::info('Test route called', [
                'requestId' => $requestId,
                'data' => $request->all()
            ]);
            return response()->json(['success' => true, 'data' => $request->all()]);
        })->name('test.conference.debug');
        Route::get('/conferences/{conferenceId}', [\App\Http\Controllers\Chair\ConferenceSetupController::class, 'show'])->name('conferences.show');
        Route::get('/conferences/{conferenceId}/edit', [\App\Http\Controllers\Chair\ConferenceSetupController::class, 'edit'])->name('conferences.edit');
        Route::put('/conferences/{conferenceId}', [\App\Http\Controllers\Chair\ConferenceSetupController::class, 'update'])->name('conferences.update');

        // Legacy Conference Configuration (keep for compatibility)
        Route::get('/my-conferences', [\App\Http\Controllers\Chair\ConferenceController::class, 'myConferences'])->name('my-conferences');
        Route::get('/configure-conference/{id}', [\App\Http\Controllers\Chair\ConferenceController::class, 'configureForm'])->name('configure-conference');
        
        // Paper Management
        Route::get('/papers', [\App\Http\Controllers\Chair\ChairController::class, 'papers'])->name('papers');
        Route::get('/papers/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'showPaper'])->name('papers.show');
        Route::get('/papers/{id}/ajax', [\App\Http\Controllers\Chair\ChairController::class, 'showPaperAjax'])->name('papers.ajax');
        
        // Reviewer Assignment
        Route::get('/papers/{id}/assign', [\App\Http\Controllers\Chair\ChairController::class, 'assignReviewers'])->name('papers.assign');
        Route::post('/papers/{id}/assign', [\App\Http\Controllers\Chair\ChairController::class, 'storeAssignment'])->name('papers.assign.store');
        Route::delete('/assignments/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'removeAssignment'])->name('assignments.remove');
        Route::get('/papers/{paperId}/coi/{reviewerId}', [\App\Http\Controllers\Chair\ChairController::class, 'checkCOI'])->name('papers.coi.check');
        Route::get('/papers/{id}/suggest-reviewers', [\App\Http\Controllers\Chair\ChairController::class, 'suggestReviewers'])->name('papers.suggest');
        
        // Phase 8.8: Reviews Management
        Route::get('/papers/{id}/reviews', [\App\Http\Controllers\Chair\ChairController::class, 'reviews'])->name('papers.reviews');
        Route::get('/papers/{id}/reviews/export', [\App\Http\Controllers\Chair\ChairController::class, 'exportReviews'])->name('papers.reviews.export');
        
        // Phase 8.9: Final Decision
        Route::get('/papers/{id}/decision', [\App\Http\Controllers\Chair\ChairController::class, 'makeDecision'])->name('papers.decision');
        Route::post('/papers/{id}/decision', [\App\Http\Controllers\Chair\ChairController::class, 'storeDecision'])->name('papers.decision.store');
        
        // Phase 8.10: Reviewers Management
        Route::get('/reviewers', [\App\Http\Controllers\Chair\ChairController::class, 'listReviewers'])->name('reviewers.index');
        Route::get('/reviewers/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'showReviewer'])->name('reviewers.show');
        
        // Phase 8.10: COI Management
        Route::get('/coi', [\App\Http\Controllers\Chair\COIController::class, 'index'])->name('coi.index');
        Route::get('/coi/{id}', [\App\Http\Controllers\Chair\COIController::class, 'show'])->name('coi.show');
        Route::get('/coi/{id}/resolve', [\App\Http\Controllers\Chair\COIController::class, 'resolveForm'])->name('coi.resolve-form');
        Route::post('/coi/{id}/resolve', [\App\Http\Controllers\Chair\COIController::class, 'resolve'])->name('coi.resolve');
        Route::get('/conferences/{conferenceId}/coi-statistics', [\App\Http\Controllers\Chair\COIController::class, 'statistics'])->name('coi.statistics');
    });
    
    // Admin Routes
    Route::prefix('admin')->middleware('role:ADMIN')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // Join Request Management
        Route::get('/join-requests', [\App\Http\Controllers\ConferenceController::class, 'adminJoinRequests'])->name('join-requests.index');
        Route::get('/conferences/{id}/join-requests', [\App\Http\Controllers\ConferenceController::class, 'manageJoinRequests'])->name('conferences.join-requests');
        Route::post('/join-requests/{id}/process', [\App\Http\Controllers\ConferenceController::class, 'processJoinRequest'])->name('join-requests.process');
        
        // Conference Request Management (Chair Request to Organize)
        Route::get('/conference-requests', [AdminConferenceRequestController::class, 'index'])->name('conference-requests.index');
        Route::get('/conference-requests/{id}', [AdminConferenceRequestController::class, 'show'])->name('conference-requests.show');
        Route::post('/conference-requests/{id}/approve', [AdminConferenceRequestController::class, 'approve'])->name('conference-requests.approve');
        Route::post('/conference-requests/{id}/reject', [AdminConferenceRequestController::class, 'reject'])->name('conference-requests.reject');
        Route::get('/conference-requests/{id}/download', [AdminConferenceRequestController::class, 'downloadProposal'])->name('conference-requests.download');
        Route::post('/conference-requests/bulk-action', [AdminConferenceRequestController::class, 'bulkAction'])->name('conference-requests.bulk-action');

        // Conference Configuration Final Approval
        Route::get('/configured-conferences', [AdminConferenceRequestController::class, 'configuredConferences'])->name('configured-conferences.index');
        Route::get('/configured-conferences/{id}', [AdminConferenceRequestController::class, 'showConference'])->name('configured-conferences.show');
        Route::post('/conference-requests/{id}/approve-conference', [AdminConferenceRequestController::class, 'approveConference'])->name('conference-requests.approve-conference');
        Route::post('/conference-requests/{id}/reject-conference', [AdminConferenceRequestController::class, 'rejectConference'])->name('conference-requests.reject-conference');
        
        // User Management
        Route::get('/users', [DashboardController::class, 'adminUsers'])->name('users.index');
        
        // Conference Management (All Conferences - Active ones)
        Route::get('/conferences', [AdminConferenceRequestController::class, 'allConferences'])->name('conferences.index');
        Route::get('/conferences/{id}', [AdminConferenceRequestController::class, 'showConferenceDetails'])->name('conferences.show');
        Route::get('/conferences/{id}/edit', [AdminConferenceRequestController::class, 'editConference'])->name('conferences.edit');
        Route::put('/conferences/{id}', [AdminConferenceRequestController::class, 'updateConference'])->name('conferences.update');
        Route::post('/conferences/{id}/status', [AdminConferenceRequestController::class, 'changeConferenceStatus'])->name('conferences.change-status');
        Route::delete('/conferences/{id}', [AdminConferenceRequestController::class, 'deleteConference'])->name('conferences.delete');
        Route::post('/conferences/bulk-delete', [AdminConferenceRequestController::class, 'bulkDelete'])->name('conferences.bulk-delete');
        
        // Reports & Statistics
        Route::get('/reports', [DashboardController::class, 'adminReports'])->name('reports.index');
        
        // User Management
        Route::get('/users', [DashboardController::class, 'adminUsers'])->name('users.index');
        Route::post('/users', [DashboardController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{id}/edit', [DashboardController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{id}', [DashboardController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [DashboardController::class, 'deleteUser'])->name('users.destroy');
        
        // Role Management
        Route::get('/roles', [DashboardController::class, 'adminRoles'])->name('roles.index');
        
        // Permissions Management
        Route::get('/permissions', [DashboardController::class, 'adminPermissions'])->name('permissions.index');
        
        // System Settings
        Route::get('/settings', [DashboardController::class, 'adminSettings'])->name('settings.index');
        
        // System Logs
        Route::get('/logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('/logs/stats', [\App\Http\Controllers\Admin\ActivityLogController::class, 'stats'])->name('logs.stats');
        Route::get('/logs/export', [\App\Http\Controllers\Admin\ActivityLogController::class, 'export'])->name('logs.export');
        Route::delete('/logs/clear', [\App\Http\Controllers\Admin\ActivityLogController::class, 'clear'])->name('logs.clear');
        Route::get('/logs/{id}', [\App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('logs.show');
        
        // Email Verification Management
        Route::post('/users/{id}/verify-email', [DashboardController::class, 'verifyUserEmail'])->name('users.verify-email');
        Route::post('/users/{id}/unverify-email', [DashboardController::class, 'unverifyUserEmail'])->name('users.unverify-email');
        
        // User API Routes
        Route::get('/api/users/{id}', [DashboardController::class, 'getUserDetails'])->name('users.api.details');
        Route::post('/api/users/bulk-delete', [DashboardController::class, 'bulkDeleteUsers'])->name('users.api.bulk-delete');
        Route::post('/api/users/{id}/role', [DashboardController::class, 'updateUserRole'])->name('users.api.update-role');
        
        // Conference API Routes
        Route::get('/api/conferences/{id}', [DashboardController::class, 'getConferenceDetails'])->name('conferences.api.details');
        Route::post('/api/conferences/bulk-delete', [DashboardController::class, 'bulkDeleteConferences'])->name('conferences.api.bulk-delete');
    });
});

// Debug route
Route::get('/chair/debug-menu', function () {
    return view('chair.debug-menu');
})->name('chair.debug-menu')->middleware(['auth']);
