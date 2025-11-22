<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Author\PaperController as AuthorPaperController;
use App\Http\Controllers\Reviewer\BiddingController;
use App\Http\Controllers\Reviewer\AssignmentController;
use App\Http\Controllers\Chair\ConferenceController as ChairConferenceController;
use App\Http\Controllers\Chair\PaperController as ChairPaperController;
use App\Http\Controllers\Chair\ReviewerController as ChairReviewerController;
use App\Http\Controllers\Chair\ReportController as ChairReportController;
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

// Public News Routes (Article listing)
Route::get('/tin-tuc', [\App\Http\Controllers\NewsController::class, 'index'])->name('articles.index');
Route::get('/tin-tuc/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])->name('articles.show');

// News Page (Dashboard-style)
Route::get('/news', [HomeController::class, 'news'])->name('news.index');

// Debug route - Check current logged in user
Route::get('/debug/whoami', function() {
    if (Auth::check()) {
        return response()->json([
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'full_name' => Auth::user()->full_name,
            'name' => Auth::user()->name,
        ]);
    }
    return response()->json(['message' => 'Not logged in']);
});

// AJAX Routes for Homepage
Route::get('/api/search-conferences', [HomeController::class, 'searchConferences'])->name('api.search.conferences');
Route::get('/api/conference-counts', [HomeController::class, 'getConferenceCounts'])->name('api.conference.counts');

// API: Get news summary for announcement creation
Route::get('/api/news/{id}/summary', function($id) {
    $news = DB::table('news')->where('news_id', $id)->first();

    if (!$news) {
        return response()->json(['error' => 'News not found'], 404);
    }

    return response()->json([
        'title' => $news->title,
        'summary' => $news->summary,
        'slug' => $news->slug,
        'url' => url('/tin-tuc/' . $news->slug)
    ]);
})->name('api.news.summary');

// Assignment Notification Routes
Route::middleware(['auth', 'role:REVIEWER'])->prefix('reviewer')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\Reviewer\NotificationController::class, 'index'])->name('reviewer.notifications.index');
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Reviewer\NotificationController::class, 'markAsRead'])->name('reviewer.notifications.read');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Reviewer\NotificationController::class, 'getUnreadCount'])->name('reviewer.notifications.unread_count');
    Route::patch('/notifications/mark-all-read', [\App\Http\Controllers\Reviewer\NotificationController::class, 'markAllAsRead'])->name('reviewer.notifications.mark_all_read');

    // Assignment tracking routes
    Route::get('/assignments', [\App\Http\Controllers\Reviewer\AssignmentController::class, 'index'])->name('reviewer.assignments.index');
    Route::get('/assignments/{id}', [\App\Http\Controllers\Reviewer\AssignmentController::class, 'show'])->name('reviewer.assignments.show');

    // UI Animations Demo
    Route::get('/animations-demo', function () {
        return view('animations-demo');
    })->name('reviewer.animations.demo');
    Route::post('/assignments/{id}/accept', [\App\Http\Controllers\Reviewer\AssignmentController::class, 'accept'])->name('reviewer.assignments.accept');
    Route::post('/assignments/{id}/decline', [\App\Http\Controllers\Reviewer\AssignmentController::class, 'decline'])->name('reviewer.assignments.decline');
    Route::get('/assignments/stats', [\App\Http\Controllers\Reviewer\AssignmentController::class, 'getStats'])->name('reviewer.assignments.stats');
});
// Conference Routes (Public)
Route::get('/conferences', [\App\Http\Controllers\ConferenceController::class, 'index'])->name('conferences.index');
Route::get('/conferences/{id}', [\App\Http\Controllers\ConferenceController::class, 'show'])->name('conferences.show');
Route::get('/conferences/{id}/cfp', [\App\Http\Controllers\ConferenceController::class, 'showCFP'])->name('conferences.cfp');

// Reviewer invitation routes (public access)
Route::get('/reviewer/invitation/{token}', [\App\Http\Controllers\Reviewer\InvitationController::class, 'acceptInvitation'])->name('reviewer.invitation.accept');
Route::get('/reviewer/join', [\App\Http\Controllers\Reviewer\InvitationController::class, 'showJoinForm'])->name('reviewer.join.form');
Route::post('/reviewer/join', [\App\Http\Controllers\Reviewer\InvitationController::class, 'submitJoinForm'])->name('reviewer.join.submit');

// Conference Join Request Routes (Authenticated and Verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/conferences/{id}/join-request', [\App\Http\Controllers\ConferenceController::class, 'submitJoinRequest'])->name('conferences.join-request');
    Route::get('/conferences/{id}/my-requests', [\App\Http\Controllers\ConferenceController::class, 'getUserJoinRequests'])->name('conferences.my-requests');
    Route::get('/my-join-requests', [\App\Http\Controllers\ConferenceController::class, 'myJoinRequests'])->name('join-requests.index');
});

Route::get('/process', [HomeController::class, 'process'])->name('process');
Route::get('/support', [HomeController::class, 'support'])->name('support');



// Conference Request Routes (Authenticated and Verified)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/create-conference', function () {
        return view('conference-request.create');
    })->name('conference-request.create');



    Route::post('/conference-requests', [ConferenceRequestController::class, 'store'])->name('conference-request.store');

    // Conference Management Routes
    Route::prefix('conference-management')->name('conference-management.')->group(function () {
        Route::get('/requests', [\App\Http\Controllers\ConferenceManagementController::class, 'requests'])->name('requests');
        Route::get('/requests/{id}', [\App\Http\Controllers\ConferenceManagementController::class, 'showRequest'])->name('request.show');
        Route::post('/requests/{id}/approve', [\App\Http\Controllers\ConferenceManagementController::class, 'approveRequest'])->name('request.approve');
        Route::post('/requests/{id}/reject', [\App\Http\Controllers\ConferenceManagementController::class, 'rejectRequest'])->name('request.reject');
    });
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

        // Parse and store co-chairs
        if ($request->has('co_chairs') && $request->co_chairs) {
            \Log::info('[WEB ROUTE] Processing co-chairs', ['co_chairs' => $request->co_chairs]);

            $coChairs = json_decode($request->co_chairs, true);

            if (is_array($coChairs) && count($coChairs) > 0) {
                \Log::info('[WEB ROUTE] Decoded co-chairs', ['count' => count($coChairs)]);

                foreach ($coChairs as $index => $coChair) {
                    if (!empty($coChair['fullname']) && !empty($coChair['email'])) {
                        $created = \App\Models\ThemVienBoSung::create([
                            'request_id' => $conferenceRequest->request_id,
                            'fullname' => $coChair['fullname'],
                            'email' => $coChair['email'],
                            'affiliation' => $coChair['affiliation'] ?? null,
                        ]);
                        \Log::info("[WEB ROUTE] Co-chair #{$index} created", ['id' => $created->co_chair_id]);
                    }
                }
            } else {
                \Log::info('[WEB ROUTE] No valid co-chairs data');
            }
        } else {
            \Log::info('[WEB ROUTE] No co-chairs in request');
        }

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

        // Reviews
        Route::get('/reviews', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'reviews'])->name('reviews');
        Route::get('/reviews/create/{assignmentId}', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'createReview'])->name('reviews.create');
        Route::post('/reviews/{assignmentId}/store', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'storeReview'])->name('reviews.store');
        Route::get('/reviews/{assignmentId}/data', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'getReviewData'])->name('reviews.data');
        Route::get('/reviews/{id}', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'showReview'])->name('reviews.show');
        Route::get('/reviews/{id}/edit', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'editReview'])->name('reviews.edit');
        Route::put('/reviews/{id}', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'updateReview'])->name('reviews.update');

        // Paper Download
        Route::get('/papers/{paperId}/download', [\App\Http\Controllers\Reviewer\ReviewerController::class, 'downloadPaper'])->name('papers.download');

        // Bidding & COI Management
        Route::get('/bidding', [BiddingController::class, 'index'])->name('bidding');
        Route::get('/conferences', [BiddingController::class, 'getConferences'])->name('conferences');
        Route::get('/conference/{conferenceId}/papers', [BiddingController::class, 'getConferencePapers'])->name('conference.papers');
        Route::post('/bidding', [BiddingController::class, 'submitBidding'])->name('bidding.submit');
        Route::post('/bidding/bulk', [BiddingController::class, 'submitBulkBidding'])->name('bidding.bulk');
        Route::get('/assignments/data', [BiddingController::class, 'getAssignments'])->name('assignments.data');
        Route::post('/assignment/{assignmentId}/respond', [BiddingController::class, 'respondToAssignment'])->name('assignment.respond');
        Route::get('/bidding/statistics/{conferenceId?}', [BiddingController::class, 'getBiddingStatistics'])->name('bidding.statistics');

        // Phase 8.10: Legacy COI Management (keeping for compatibility)
        Route::get('/coi', [\App\Http\Controllers\Reviewer\COIController::class, 'index'])->name('coi.index');
        Route::get('/coi/create', [\App\Http\Controllers\Reviewer\COIController::class, 'create'])->name('coi.create');
        Route::post('/coi', [\App\Http\Controllers\Reviewer\COIController::class, 'store'])->name('coi.store');
        Route::get('/coi/{id}', [\App\Http\Controllers\Reviewer\COIController::class, 'show'])->name('coi.show');
        Route::delete('/coi/{id}', [\App\Http\Controllers\Reviewer\COIController::class, 'retract'])->name('coi.retract');
        Route::get('/coi/search-papers', [\App\Http\Controllers\Reviewer\COIController::class, 'searchPapers'])->name('coi.search-papers');
    });

    // Web-based Notification Routes (for authenticated users)
    Route::middleware('auth')->prefix('web')->group(function () {
        Route::get('/notifications', function () {
            $user = Auth::user();

            \Log::info('Notification request', [
                'user' => $user ? $user->user_id : 'null',
                'name' => $user ? $user->full_name : 'null'
            ]);

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $notifications = DB::table('user_notifications')
                ->where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($notif) {
                    return [
                        'id' => $notif->notification_id,
                        'title' => $notif->title,
                        'message' => $notif->message,
                        'time' => \Carbon\Carbon::parse($notif->created_at)->diffForHumans(),
                        'created_at' => $notif->created_at,
                        'is_read' => (bool)$notif->is_read,
                    ];
                });

            $unreadCount = DB::table('user_notifications')
                ->where('user_id', $user->user_id)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications,
                'unreadCount' => $unreadCount
            ]);
        })->name('web.notifications');

        Route::patch('/notifications/{id}/read', function ($id) {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $updated = DB::table('user_notifications')
                ->where('notification_id', $id)
                ->where('user_id', $user->user_id)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json(['success' => $updated > 0]);
        })->name('web.notifications.read');

        Route::patch('/notifications/read-all', function () {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $updated = DB::table('user_notifications')
                ->where('user_id', $user->user_id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json(['success' => true, 'updated' => $updated]);
        })->name('web.notifications.read_all');

        // Notification detail page
        Route::get('/notifications/{id}', function ($id) {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login');
            }

            $notification = DB::table('user_notifications')
                ->where('notification_id', $id)
                ->where('user_id', $user->user_id)
                ->first();

            if (!$notification) {
                abort(404, 'Notification not found');
            }

            // Mark as read if not already
            if (!$notification->is_read) {
                DB::table('user_notifications')
                    ->where('notification_id', $id)
                    ->update([
                        'is_read' => true,
                        'read_at' => now()
                    ]);
            }

            // Determine user role for color scheme
            $roles = DB::table('vaitronguoidung')
                ->where('user_id', $user->user_id)
                ->pluck('role_code')
                ->toArray();

            $colorScheme = 'blue'; // Default
            if (in_array('CHAIR', $roles)) {
                $colorScheme = 'orange';
            } elseif (in_array('REVIEWER', $roles)) {
                $colorScheme = 'purple';
            } elseif (in_array('AUTHOR', $roles)) {
                $colorScheme = 'blue';
            }

            return view('notifications.show', compact('notification', 'colorScheme'));
        })->name('web.notifications.show');
    });

    // Chair Routes
    Route::prefix('chair')->middleware('role:CHAIR')->name('chair.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Chair\ChairController::class, 'dashboard'])->name('dashboard');



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
        Route::get('/papers/{id}/download', [\App\Http\Controllers\Chair\ChairController::class, 'downloadPaper'])->name('papers.download');

        // Reviewer Assignment (Legacy)
        Route::get('/papers/{id}/assign', [\App\Http\Controllers\Chair\ChairController::class, 'assignReviewers'])->name('papers.assign');
        Route::post('/papers/{id}/assign', [\App\Http\Controllers\Chair\ChairController::class, 'storeAssignment'])->name('papers.assign.store');
        // Route::delete('/assignments/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'removeAssignment'])->name('assignments.remove'); // Commented out due to route conflict
        Route::get('/papers/{paperId}/coi/{reviewerId}', [\App\Http\Controllers\Chair\ChairController::class, 'checkCOI'])->name('papers.coi.check');
        Route::get('/papers/{id}/suggest-reviewers', [\App\Http\Controllers\Chair\ChairController::class, 'suggestReviewers'])->name('papers.suggest');

        // Phase 8.8: Reviews Management
        Route::get('/papers/{id}/reviews', [\App\Http\Controllers\Chair\ChairController::class, 'reviews'])->name('papers.reviews');
        Route::get('/papers/{id}/reviews/export', [\App\Http\Controllers\Chair\ChairController::class, 'exportReviews'])->name('papers.reviews.export');
        Route::get('/reviews/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'getReviewDetails'])->name('reviews.details');
        Route::get('/test-review/{id}', function($id) {
            $review = DB::table('phanbien as pb')
                ->join('reviewer_assignments as ra', 'pb.assignment_id', '=', 'ra.id')
                ->join('nguoidung as u', 'ra.user_id', '=', 'u.user_id')
                ->where('pb.review_id', $id)
                ->select(['pb.*', 'u.full_name as reviewer_name', 'u.email as reviewer_email', 'u.organization as reviewer_organization'])
                ->first();

            if (!$review) {
                return response()->json(['error' => 'Review not found'], 404);
            }

            return response()->json($review);
        });

        // Phase 8.9: Final Decision
        Route::get('/papers/{id}/decision', [\App\Http\Controllers\Chair\ChairController::class, 'makeDecision'])->name('papers.decision');
        Route::post('/papers/{id}/decision', [\App\Http\Controllers\Chair\ChairController::class, 'storeDecision'])->name('papers.decision.store');

        // Phase 8.10: Reviewers Management - Direct to invitation controller
        Route::get('/reviewers', [\App\Http\Controllers\Chair\ReviewerInvitationController::class, 'index'])->name('reviewers.index');

        // Phase 8.10: COI Management
        Route::get('/coi', [\App\Http\Controllers\Chair\COIController::class, 'index'])->name('coi.index');
        Route::get('/coi/{id}', [\App\Http\Controllers\Chair\COIController::class, 'show'])->name('coi.show');
        Route::get('/coi/{id}/resolve', [\App\Http\Controllers\Chair\COIController::class, 'resolveForm'])->name('coi.resolve-form');
        Route::post('/coi/{id}/resolve', [\App\Http\Controllers\Chair\COIController::class, 'resolve'])->name('coi.resolve');
        Route::get('/conferences/{conferenceId}/coi-statistics', [\App\Http\Controllers\Chair\COIController::class, 'statistics'])->name('coi.statistics');

        // Reviewer Invitation Management
        Route::get('/reviewers/invite', [\App\Http\Controllers\Chair\ReviewerInvitationController::class, 'index'])->name('reviewers.invite');
        Route::post('/reviewers/invite/send', [\App\Http\Controllers\Chair\ReviewerInvitationController::class, 'sendInvitation'])->name('reviewers.invite.send');
        Route::get('/reviewers/invitations', [\App\Http\Controllers\Chair\ReviewerInvitationController::class, 'sentInvitations'])->name('reviewers.invite.list');
        Route::post('/reviewers/invite/{id}/resend', [\App\Http\Controllers\Chair\ReviewerInvitationController::class, 'resendInvitation'])->name('reviewers.invite.resend');
        Route::post('/reviewers/invite/{id}/revoke', [\App\Http\Controllers\Chair\ReviewerInvitationController::class, 'revokeInvitation'])->name('reviewers.invite.revoke');

        // Test route
        Route::get('/test-invite', function() {
            return view('chair.reviewers.invite', ['conferences' => collect()]);
        })->name('test.invite');

        // Conference Reminder Management
        Route::get('/reminders', [\App\Http\Controllers\Chair\ConferenceReminderController::class, 'index'])->name('reminders.index');
        Route::get('/reminders/{conferenceId}/logs', [\App\Http\Controllers\Chair\ConferenceReminderController::class, 'logs'])->name('reminders.logs');
        Route::post('/reminders/test-send', [\App\Http\Controllers\Chair\ConferenceReminderController::class, 'testSend'])->name('reminders.test-send');

        // Bidding Settings
        Route::get('/bidding-settings', [\App\Http\Controllers\Chair\BiddingSettingsController::class, 'index'])->name('bidding-settings');
        Route::get('/conferences/{conferenceId}/bidding-settings', [\App\Http\Controllers\Chair\BiddingSettingsController::class, 'getSettings'])->name('bidding-settings.get');
        Route::put('/conferences/{conferenceId}/bidding-settings', [\App\Http\Controllers\Chair\BiddingSettingsController::class, 'updateSettings'])->name('bidding-settings.update');
        Route::get('/conferences/{conferenceId}/bidding-statistics', [\App\Http\Controllers\Chair\BiddingSettingsController::class, 'getStatistics'])->name('bidding-settings.statistics');

        // News & Events Management
        Route::resource('news', \App\Http\Controllers\Chair\NewsController::class);

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/unread', [\App\Http\Controllers\Chair\NotificationController::class, 'getUnread'])->name('unread');
            Route::post('/{id}/read', [\App\Http\Controllers\Chair\NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [\App\Http\Controllers\Chair\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::get('/{id}/redirect', [\App\Http\Controllers\Chair\NotificationController::class, 'markAsReadAndRedirect'])->name('redirect');
        });

        // === Báo cáo & Thống kê ===
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ChairReportController::class, 'index'])->name('index');
            Route::get('/export/{conferenceId}', [ChairReportController::class, 'exportReport'])->name('export');
        });

        // === NEW: Reviewer Assignment Management System ===
        Route::prefix('assignments')->name('assignments.')->group(function () {
            // Dashboard phân công
            Route::get('/', [\App\Http\Controllers\Chair\AssignmentController::class, 'index'])->name('index');

            // Chi tiết bidding cho một bài
            Route::get('/papers/{id}/bidding', [\App\Http\Controllers\Chair\AssignmentController::class, 'showPaperBidding'])->name('papers.bidding');

            // Phân công reviewers
            Route::post('/papers/{id}/assign', [\App\Http\Controllers\Chair\AssignmentController::class, 'assignReviewers'])->name('papers.assign');

            // Hủy phân công
            Route::delete('/papers/{paperId}/reviewers/{reviewerId}', [\App\Http\Controllers\Chair\AssignmentController::class, 'unassignReviewer'])->name('papers.unassign');

            // Tự động phân công
            Route::post('/conferences/{id}/auto-assign', [\App\Http\Controllers\Chair\AssignmentController::class, 'autoAssignConference'])->name('conferences.auto-assign');

            // Thống kê
            Route::get('/conferences/{id}/stats', [\App\Http\Controllers\Chair\AssignmentController::class, 'getAssignmentStats'])->name('conferences.stats');
        });

        // === Báo cáo thống kê Chair ===
        Route::prefix('reports')->name('reports.')->group(function () {
            // Dashboard báo cáo
            Route::get('/', [\App\Http\Controllers\Chair\ReportController::class, 'index'])->name('index');

            // Báo cáo bài báo
            Route::get('/papers/{conferenceId}', [\App\Http\Controllers\Chair\ReportController::class, 'paperStatistics'])->name('papers');

            // Báo cáo reviewers
            Route::get('/reviewers/{conferenceId}', [\App\Http\Controllers\Chair\ReportController::class, 'reviewerStatistics'])->name('reviewers');

            // Báo cáo bidding
            Route::get('/bidding/{conferenceId}', [\App\Http\Controllers\Chair\ReportController::class, 'biddingStatistics'])->name('bidding');

            // Export báo cáo
            Route::get('/export/{conferenceId}', [\App\Http\Controllers\Chair\ReportController::class, 'exportReport'])->name('export');
        });

        // Route with parameter should be last to avoid conflicts
        Route::get('/reviewers/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'showReviewer'])->name('reviewers.show');

        // Reviewer Assignment Management (New Bidding System)
        Route::get('/assignments', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/statistics/{conferenceId}', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'getAssignmentStatistics'])->name('assignments.statistics');
        Route::get('/assignments/papers/{conferenceId}', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'getConferencePapers'])->name('assignments.papers');
        Route::get('/assignments/paper/{paperId}/biddings', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'getPaperBiddings'])->name('assignments.paper.biddings');
        Route::post('/assignments/manual-assign', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'manualAssign'])->name('assignments.manual');
        Route::post('/assignments/auto-assign', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'autoAssign'])->name('assignments.auto');
        Route::delete('/assignments/{assignmentId}', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'removeAssignment'])->name('reviewer.assignments.remove');

        // Legacy routes for backward compatibility
        Route::get('/assignments/{paperId}/assign', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'assign'])->name('assignments.assign');
        Route::post('/assignments/store', [\App\Http\Controllers\Chair\ReviewerAssignmentController::class, 'store'])->name('assignments.store');

        // Announcement/Notification Management
        Route::get('/announcements', function() {
            return view('chair.announcements.index');
        })->name('announcements.index');

        Route::get('/announcements/data/list', function(Request $request) {
            $userId = auth()->id();

            // Get conference IDs where user is CHAIR
            $conferenceIds = DB::table('vaitronguoidung')
                ->where('user_id', $userId)
                ->where('role_code', 'CHAIR')
                ->whereNotNull('conference_id')
                ->pluck('conference_id');

            $query = DB::table('thongbao as tb')
                ->leftJoin('hoithao as ht', 'tb.conference_id', '=', 'ht.conference_id')
                ->select(
                    'tb.announcement_id as id',
                    'tb.title',
                    'tb.content',
                    'tb.audience',
                    'tb.channels',
                    'tb.status',
                    'tb.scheduled_at as schedule_time',
                    'tb.sent_at',
                    'tb.created_at',
                    'ht.title as conference_name'
                )
                ->whereIn('tb.conference_id', $conferenceIds)
                ->orderBy('tb.created_at', 'desc');

            // Filter by status
            if ($request->status) {
                $query->where('tb.status', $request->status);
            }

            // Search
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('tb.title', 'like', '%' . $request->search . '%')
                      ->orWhere('tb.content', 'like', '%' . $request->search . '%');
                });
            }

            $announcements = $query->get()->map(function($item) {
                $item->channels = json_decode($item->channels, true) ?? [];

                // Count recipients (all active users for broadcast)
                $item->recipient_count = DB::table('user_notifications')
                    ->where('announcement_id', $item->id)
                    ->count();

                // If no records yet (not sent), estimate
                if ($item->recipient_count === 0 && $item->status !== 'SENT') {
                    $item->recipient_count = DB::table('nguoidung')
                        ->where('locked', 0)
                        ->count();
                }

                return $item;
            });

            // Calculate stats (only for user's conferences)
            $stats = [
                'total' => DB::table('thongbao')
                    ->whereIn('conference_id', $conferenceIds)
                    ->count(),
                'sent' => DB::table('thongbao')
                    ->where('status', 'SENT')
                    ->whereIn('conference_id', $conferenceIds)
                    ->count(),
                'scheduled' => DB::table('thongbao')
                    ->where('status', 'SCHEDULED')
                    ->whereIn('conference_id', $conferenceIds)
                    ->count(),
                'failed' => DB::table('thongbao')
                    ->where('status', 'FAILED')
                    ->whereIn('conference_id', $conferenceIds)
                    ->count(),
            ];

            return response()->json([
                'announcements' => $announcements,
                'stats' => $stats
            ]);
        })->name('announcements.data.list');

        Route::get('/announcements/create', function() {
            return view('chair.announcements.create');
        })->name('announcements.create');

        Route::get('/announcements/{id}/statistics', function($id) {
            return view('chair.announcements.statistics', ['id' => $id]);
        })->name('announcements.statistics');

        // AJAX endpoints for announcements
        Route::get('/announcements/data/conferences', function() {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'Not authenticated', 'data' => []], 401);
            }

            $conferences = DB::table('hoithao as ht')
                ->join('vaitronguoidung as vtn', function($join) use ($userId) {
                    $join->on('vtn.conference_id', '=', 'ht.conference_id')
                         ->where('vtn.user_id', '=', $userId)
                         ->where('vtn.role_code', '=', 'CHAIR');
                })
                ->select('ht.conference_id as id', 'ht.title as ten_hoi_thao', 'ht.start_date', 'ht.end_date', 'ht.status')
                ->orderBy('ht.start_date', 'desc')
                ->get();

            return response()->json(['data' => $conferences]);
        })->name('announcements.data.conferences');

        // NEW: Get active users count for broadcast
        Route::get('/announcements/data/active-users-count', function() {
            try {
                $count = DB::table('nguoidung')
                    ->where('locked', 0)
                    ->count();

                return response()->json(['count' => $count]);
            } catch (\Exception $e) {
                \Log::error('Failed to count active users: ' . $e->getMessage());
                return response()->json(['count' => 0], 500);
            }
        })->name('announcements.data.active-users-count');

        Route::post('/announcements/data/recipient-count', function(Request $request) {
            try {
                $conferenceId = $request->input('conference_id');
                $audience = $request->input('audience');

                \Log::info('Counting recipients', [
                    'conference_id' => $conferenceId,
                    'audience' => $audience
                ]);

                if (!$conferenceId || !$audience) {
                    return response()->json(['count' => 0]);
                }

                $count = 0;

                switch ($audience) {
                    case 'ALL':
                        // Tất cả users có liên quan đến conference
                        $count = DB::table('vaitronguoidung')
                            ->where('conference_id', $conferenceId)
                            ->distinct()
                            ->count('user_id');
                        break;

                    case 'AUTHORS':
                        // Tác giả = users có bài báo trong conference
                        $count = DB::table('baibao')
                            ->where('conference_id', $conferenceId)
                            ->whereNotNull('submitter_id')
                            ->distinct()
                            ->count('submitter_id');
                        break;

                    case 'REVIEWERS':
                        // Phản biện = users có role REVIEWER
                        $count = DB::table('vaitronguoidung')
                            ->where('conference_id', $conferenceId)
                            ->where('role_code', 'REVIEWER')
                            ->count();
                        break;

                    case 'CHAIRS':
                        // Chairs = users có role CHAIR
                        $count = DB::table('vaitronguoidung')
                            ->where('conference_id', $conferenceId)
                            ->where('role_code', 'CHAIR')
                            ->count();
                        break;
                }

                \Log::info('Recipients counted', ['count' => $count]);

                return response()->json(['count' => $count]);

            } catch (\Exception $e) {
                \Log::error('Failed to count recipients: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'count' => 0,
                    'error' => $e->getMessage()
                ], 500);
            }
        })->name('announcements.data.recipient-count');

        Route::post('/announcements/data/recipient-list', function(Request $request) {
            try {
                $conferenceId = $request->input('conference_id');
                $audience = $request->input('audience');

                \Log::info('Loading recipient list', [
                    'conference_id' => $conferenceId,
                    'audience' => $audience
                ]);

                if (!$conferenceId || !$audience) {
                    return response()->json(['users' => []]);
                }

                $users = collect();

                switch ($audience) {
                    case 'ALL':
                        // Tất cả users có liên quan đến conference
                        $users = DB::table('nguoidung as u')
                            ->join('vaitronguoidung as vtn', 'vtn.user_id', '=', 'u.user_id')
                            ->where('vtn.conference_id', $conferenceId)
                            ->select('u.user_id', 'u.full_name', 'u.email')
                            ->distinct()
                            ->get();
                        break;

                    case 'AUTHORS':
                        // Tác giả = users có bài báo trong conference
                        $users = DB::table('nguoidung as u')
                            ->join('baibao as bb', 'bb.submitter_id', '=', 'u.user_id')
                            ->where('bb.conference_id', $conferenceId)
                            ->select('u.user_id', 'u.full_name', 'u.email')
                            ->distinct()
                            ->get();
                        break;

                    case 'REVIEWERS':
                        // Phản biện = users có role REVIEWER
                        $users = DB::table('nguoidung as u')
                            ->join('vaitronguoidung as vtn', 'vtn.user_id', '=', 'u.user_id')
                            ->where('vtn.conference_id', $conferenceId)
                            ->where('vtn.role_code', 'REVIEWER')
                            ->select('u.user_id', 'u.full_name', 'u.email')
                            ->distinct()
                            ->get();
                        break;

                    case 'CHAIRS':
                        // Chairs = users có role CHAIR
                        $users = DB::table('nguoidung as u')
                            ->join('vaitronguoidung as vtn', 'vtn.user_id', '=', 'u.user_id')
                            ->where('vtn.conference_id', $conferenceId)
                            ->where('vtn.role_code', 'CHAIR')
                            ->select('u.user_id', 'u.full_name', 'u.email')
                            ->distinct()
                            ->get();
                        break;
                }

                \Log::info('Recipients loaded', ['count' => $users->count()]);

                return response()->json(['users' => $users]);

            } catch (\Exception $e) {
                \Log::error('Failed to load recipient list: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'users' => [],
                    'error' => $e->getMessage()
                ], 500);
            }
        })->name('announcements.data.recipient-list');

        Route::post('/announcements/store', function(Request $request) {
            try {
                // Simplified validation for broadcast notifications
                $validated = $request->validate([
                    'title' => 'required|string|max:255',
                    'content' => 'required|string',
                    'channels' => 'required|array',
                    'channels.*' => 'in:email,in-app',
                    'send_immediately' => 'boolean',
                    'schedule_time' => 'nullable|date|after:now'
                ]);

                \Log::info('Creating broadcast announcement', [
                    'title' => $validated['title'],
                    'channels' => $validated['channels'],
                    'schedule_time' => $validated['schedule_time'] ?? null
                ]);

                $user = Auth::user();

                // Prepare channels (convert to uppercase for consistency)
                $channels = array_map('strtoupper', $validated['channels']);
                // Map 'in-app' to 'SYSTEM' for database consistency
                $channels = array_map(function($ch) {
                    return $ch === 'IN-APP' ? 'SYSTEM' : $ch;
                }, $channels);
                $channelsJson = json_encode($channels);

                // Determine status and time
                $sendImmediately = $validated['send_immediately'] ?? true;
                $scheduleTime = $validated['schedule_time'] ?? null;

                $status = 'DRAFT';
                if ($sendImmediately && !$scheduleTime) {
                    $status = 'SENT';
                } elseif ($scheduleTime) {
                    $status = 'SCHEDULED';
                }

                // Save announcement to database
                // Note: conference_id and audience are NOT required for broadcast
                $announcementId = DB::table('thongbao')->insertGetId([
                    'conference_id' => null, // Broadcast: no specific conference
                    'created_by' => $user->user_id,
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'audience' => 'ALL', // Broadcast always targets ALL
                    'scheduled_at' => $scheduleTime,
                    'sent_at' => ($status === 'SENT') ? now() : null,
                    'status' => $status,
                    'channels' => $channelsJson,
                    'created_at' => now()
                ]);

                \Log::info("Broadcast announcement created with ID: {$announcementId}, Status: {$status}");

                // If sending immediately
                if ($status === 'SENT') {
                    // Dispatch job to send notification
                    \App\Jobs\SendAnnouncementJob::dispatch($announcementId, $channels);

                    return response()->json([
                        'success' => true,
                        'message' => 'Thông báo đang được gửi đến tất cả người dùng...'
                    ]);
                }

                // If scheduled
                if ($status === 'SCHEDULED') {
                    return response()->json([
                        'success' => true,
                        'message' => "Thông báo đã được lên lịch gửi vào " . \Carbon\Carbon::parse($scheduleTime)->format('d/m/Y H:i')
                    ]);
                }

                // If draft
                return response()->json([
                    'success' => true,
                    'message' => 'Thông báo đã được lưu nháp'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Failed to create broadcast announcement: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
                ], 500);
            }
        })->name('announcements.store');
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
        Route::get('/reports', [\App\Http\Controllers\Admin\AdminReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/data', [\App\Http\Controllers\Admin\AdminReportController::class, 'data'])->name('reports.data');

        // News & Events Management
        Route::resource('news', \App\Http\Controllers\Admin\NewsController::class);
        Route::post('/news/{id}/approve', [\App\Http\Controllers\Admin\NewsController::class, 'approve'])->name('news.approve');
        Route::post('/news/{id}/reject', [\App\Http\Controllers\Admin\NewsController::class, 'reject'])->name('news.reject');

        // Notifications Management
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('/notifications/unread', [\App\Http\Controllers\Admin\NotificationController::class, 'getUnread'])->name('notifications.unread');
        Route::get('/notifications/{id}/redirect', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsReadAndRedirect'])->name('notifications.redirect');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.delete');

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

        // System Settings (Backup & Restore)
        Route::get('/settings', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('settings.index');
        Route::post('/settings/update', [\App\Http\Controllers\Admin\BackupController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/backup', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('settings.backup.create');
        Route::get('/settings/backup/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('settings.backup.download');
        Route::delete('/settings/backup/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'delete'])->name('settings.backup.delete');
        Route::post('/settings/backup/{filename}/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('settings.backup.restore');

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

// Test join request debug
Route::get('/debug/test-join/{token}', function($token) {
    // Simulate a join request with invitation
    $invitation = DB::table('reviewer_invitations')
        ->where('token', $token)
        ->where('status', 'PENDING')
        ->first();

    if (!$invitation) {
        return 'Invitation not found';
    }

    $user = DB::table('nguoidung')->where('email', $invitation->email)->first();
    if (!$user) {
        return 'User not found with email: ' . $invitation->email;
    }

    // Check existing roles
    $existingRoles = DB::table('vaitronguoidung')
        ->where('user_id', $user->user_id)
        ->get();

    $output = "User: {$user->full_name} (ID: {$user->user_id})\n";
    $output .= "Email: {$user->email}\n";
    $output .= "Existing roles: " . count($existingRoles) . "\n";
    foreach($existingRoles as $role) {
        $output .= "- {$role->role_code} for conference {$role->conference_id}\n";
    }

    // Test role assignment
    $existingReviewerRole = DB::table('vaitronguoidung')
        ->where('user_id', $user->user_id)
        ->where('conference_id', $invitation->conference_id)
        ->where('role_code', 'REVIEWER')
        ->first();

    if (!$existingReviewerRole) {
        DB::table('vaitronguoidung')->insert([
            'user_id' => $user->user_id,
            'conference_id' => $invitation->conference_id,
            'role_code' => 'REVIEWER'
        ]);
        $output .= "\n✅ REVIEWER role assigned!";
    } else {
        $output .= "\n⚠️ REVIEWER role already exists";
    }

    return nl2br($output);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/role-selection', [AuthController::class, 'showRoleSelection'])->name('role.selection');
    Route::post('/select-role', [AuthController::class, 'selectRole'])->name('role.select');
});
