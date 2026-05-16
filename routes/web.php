<?php

use App\Http\Controllers\Admin\JournalEditionManageController;
use App\Http\Controllers\Admin\JournalManageController;
use App\Http\Controllers\Admin\UserManageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthorRevisionController;
use App\Http\Controllers\Dashboard\AuthorSubmissionController;
use App\Http\Controllers\Dashboard\AuthorSubmissionShowController;
use App\Http\Controllers\Dashboard\EditorPipelineController;
use App\Http\Controllers\Dashboard\EditorSubmissionShowController;
use App\Http\Controllers\Dashboard\ReviewerInboxController;
use App\Http\Controllers\Dashboard\RoleDashboardController;
use App\Http\Controllers\DashboardHubController;
use App\Http\Controllers\EditorDecisionController;
use App\Http\Controllers\EditorSubmissionActionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalSubmissionWebController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\ReviewerTaskController;
use App\Http\Controllers\ReviewInvitationController;
use App\Http\Controllers\SubmissionPublishController;
use App\Http\Controllers\SubmissionLegacyRedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/articles/{submission}', [PublicArticleController::class, 'show'])->name('journal.articles.show');

Route::get('/review-invitations/{assignment}/accept', [ReviewInvitationController::class, 'accept'])
    ->middleware(['signed'])
    ->name('review-invitations.accept');

Route::get('/review-invitations/{assignment}/decline', [ReviewInvitationController::class, 'declineForm'])
    ->middleware(['signed'])
    ->name('review-invitations.decline');

Route::post('/review-invitations/{assignment}/decline', [ReviewInvitationController::class, 'decline'])
    ->middleware(['signed'])
    ->name('review-invitations.decline.submit');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::middleware(['auth', 'journal.context'])->group(function () {
    Route::get('/submit', [JournalSubmissionWebController::class, 'create'])->name('journal.submit.create');
    Route::post('/submit', [JournalSubmissionWebController::class, 'store'])->name('journal.submit.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardHubController::class, 'index'])->name('dashboard');

    Route::get('/author/dashboard', [RoleDashboardController::class, 'author'])->name('author.dashboard');
    Route::get('/author/submissions', AuthorSubmissionController::class)->name('author.submissions');
    Route::get('/author/submissions/{submission}', [AuthorSubmissionShowController::class, 'show'])->name('author.submissions.show');
    Route::post('/author/submissions/{submission}/revision', [AuthorRevisionController::class, 'store'])->name('author.submissions.revision.store');

    Route::get('/editor/dashboard', [RoleDashboardController::class, 'editor'])->name('editor.dashboard');
    Route::get('/editor/pipeline', EditorPipelineController::class)->name('editor.pipeline');
    Route::get('/editor/submissions/{submission}', [EditorSubmissionShowController::class, 'show'])->name('editor.submissions.show');
    Route::post('/editor/submissions/{submission}/assign-reviewer', [EditorSubmissionActionController::class, 'assignReviewer'])->name('editor.submissions.assign-reviewer');
    Route::post('/editor/submissions/{submission}/decision', [EditorDecisionController::class, 'store'])->name('editor.submissions.decision');
    Route::post('/editor/submissions/{submission}/publish', [SubmissionPublishController::class, 'store'])->name('editor.submissions.publish');

    Route::get('/reviewer/dashboard', [RoleDashboardController::class, 'reviewer'])->name('reviewer.dashboard');
    Route::get('/reviewer/inbox', ReviewerInboxController::class)->name('reviewer.inbox');

    Route::get('/dashboard/author', fn () => redirect()->away(platform_route('author.submissions'), 301));
    Route::get('/dashboard/editor', fn () => redirect()->away(platform_route('editor.pipeline'), 301));
    Route::get('/dashboard/reviewer', fn () => redirect()->away(platform_route('reviewer.inbox'), 301));
    Route::get('/dashboard/admin', fn () => redirect()->away(platform_route('admin.dashboard'), 301));

    Route::get('/submissions/{submission}', SubmissionLegacyRedirectController::class)->name('submissions.show');

    Route::get('/review-tasks/{assignment}', [ReviewerTaskController::class, 'show'])->name('review-tasks.show');
    Route::post('/review-tasks/{assignment}', [ReviewerTaskController::class, 'store'])->name('review-tasks.store');
    Route::post('/review-tasks/{assignment}/accept', [ReviewerTaskController::class, 'accept'])->name('review-tasks.accept');
    Route::post('/review-tasks/{assignment}/decline', [ReviewerTaskController::class, 'decline'])->name('review-tasks.decline');

    Route::middleware('platform.admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [RoleDashboardController::class, 'admin'])->name('dashboard');

        Route::get('journals', [JournalManageController::class, 'index'])->name('journals.index');
        Route::get('journals/create', [JournalManageController::class, 'create'])->name('journals.create');
        Route::post('journals', [JournalManageController::class, 'store'])->name('journals.store');
        Route::get('journals/{journal}/editions', [JournalEditionManageController::class, 'index'])->name('journals.editions.index');
        Route::get('journals/{journal}/editions/create', [JournalEditionManageController::class, 'create'])->name('journals.editions.create');
        Route::post('journals/{journal}/editions', [JournalEditionManageController::class, 'store'])->name('journals.editions.store');
        Route::get('journals/{journal}/edit', [JournalManageController::class, 'edit'])->name('journals.edit');
        Route::put('journals/{journal}', [JournalManageController::class, 'update'])->name('journals.update');

        Route::get('users', [UserManageController::class, 'index'])->name('users.index');
        Route::get('users/{user}/roles', [UserManageController::class, 'editRoles'])->name('users.edit-roles');
        Route::put('users/{user}/roles', [UserManageController::class, 'updateRoles'])->name('users.update-roles');
    });
});
