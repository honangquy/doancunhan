<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Author\PaperController as AuthorPaperController;
use App\Http\Controllers\Reviewer\BiddingController;
use App\Http\Controllers\Reviewer\AssignmentController;
use App\Http\Controllers\Chair\ConferenceController as ChairConferenceController;
use App\Http\Controllers\Chair\PaperController as ChairPaperController;
use App\Http\Controllers\Chair\ReviewerController as ChairReviewerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ConferenceController as AdminConferenceController;
use App\Http\Controllers\Admin\ReportController;

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
Route::get('/conferences', [HomeController::class, 'conferences'])->name('conferences.index');
Route::get('/conferences/{id}', [HomeController::class, 'conferenceDetail'])->name('conferences.show');
Route::get('/news', [HomeController::class, 'news'])->name('news.index');
Route::get('/process', [HomeController::class, 'process'])->name('process');
Route::get('/support', [HomeController::class, 'support'])->name('support');

// Guest Routes (not authenticated)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');
    
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
    });
    
    // Chair Routes
    Route::prefix('chair')->middleware('role:CHAIR')->name('chair.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Chair\ChairController::class, 'dashboard'])->name('dashboard');
        
        // Paper Management
        Route::get('/papers', [\App\Http\Controllers\Chair\ChairController::class, 'papers'])->name('papers');
        Route::get('/papers/{id}', [\App\Http\Controllers\Chair\ChairController::class, 'showPaper'])->name('papers.show');
        
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
        
        // TODO: Implement these controllers
        // Route::get('/coi', [ChairCOIController::class, 'index'])->name('coi.index');
    });
    
    // Admin Routes
    Route::prefix('admin')->middleware('role:ADMIN')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        
        // TODO: Implement these controllers
        // Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        // Route::get('/conferences', [AdminConferenceController::class, 'index'])->name('conferences.index');
        // Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        // Route::get('/roles', [AdminRoleController::class, 'index'])->name('roles.index');
        // Route::get('/system', [AdminSystemController::class, 'index'])->name('system.index');
        // Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');
    });
});
