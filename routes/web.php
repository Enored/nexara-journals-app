<?php

use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\BlogManageController;
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
use App\Http\Controllers\SubmissionLegacyRedirectController;
use App\Http\Controllers\SubmissionPublishController;
use App\Http\Controllers\UserSettingsController;
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

    Route::get('/settings', [UserSettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/profile', [UserSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [UserSettingsController::class, 'updatePassword'])->name('settings.password.update');

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
        Route::get('journals/{journal}/volumes/create', [JournalEditionManageController::class, 'createVolume'])->name('journals.volumes.create');
        Route::post('journals/{journal}/volumes', [JournalEditionManageController::class, 'storeVolume'])->name('journals.volumes.store');
        Route::delete('journals/{journal}/volumes/{volume}', [JournalEditionManageController::class, 'destroyVolume'])->name('journals.volumes.destroy');
        Route::get('journals/{journal}/editions/create', [JournalEditionManageController::class, 'create'])->name('journals.editions.create');
        Route::post('journals/{journal}/editions', [JournalEditionManageController::class, 'store'])->name('journals.editions.store');
        Route::get('journals/{journal}/editions/{edition}', [JournalEditionManageController::class, 'show'])->name('journals.editions.show');
        Route::get('journals/{journal}/editions/{edition}/edit', [JournalEditionManageController::class, 'edit'])->name('journals.editions.edit');
        Route::get('journals/{journal}/editions/{edition}/publish', [JournalEditionManageController::class, 'publishForm'])->name('journals.editions.publish-form');
        Route::get('journals/{journal}/editions/{edition}/articles/add', [JournalEditionManageController::class, 'addArticleForm'])->name('journals.editions.articles.add-form');
        Route::put('journals/{journal}/editions/{edition}', [JournalEditionManageController::class, 'update'])->name('journals.editions.update');
        Route::delete('journals/{journal}/editions/{edition}', [JournalEditionManageController::class, 'destroy'])->name('journals.editions.destroy');
        Route::post('journals/{journal}/editions/{edition}/publish', [JournalEditionManageController::class, 'publishIssue'])->name('journals.editions.publish');
        Route::post('journals/{journal}/editions/{edition}/unpublish', [JournalEditionManageController::class, 'unpublishIssue'])->name('journals.editions.unpublish');
        Route::post('journals/{journal}/editions/{edition}/articles', [JournalEditionManageController::class, 'assignArticle'])->name('journals.editions.articles.assign');
        Route::delete('journals/{journal}/editions/{edition}/articles/{submission}', [JournalEditionManageController::class, 'removeArticle'])->name('journals.editions.articles.remove');
        Route::get('journals/{journal}/edit', [JournalManageController::class, 'edit'])->name('journals.edit');
        Route::put('journals/{journal}', [JournalManageController::class, 'update'])->name('journals.update');

        Route::get('users/export', [UserManageController::class, 'export'])->name('users.export');
        Route::post('users/import', [UserManageController::class, 'import'])->name('users.import');
        Route::get('users', [UserManageController::class, 'index'])->name('users.index');
        Route::post('users/{user}/suspend', [UserManageController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/unsuspend', [UserManageController::class, 'unsuspend'])->name('users.unsuspend');
        Route::post('users/{user}/impersonate', [UserManageController::class, 'impersonate'])->name('users.impersonate');
        Route::get('users/{user}/roles', [UserManageController::class, 'editRoles'])->name('users.edit-roles');
        Route::put('users/{user}/roles', [UserManageController::class, 'updateRoles'])->name('users.update-roles');

        Route::get('blogs', [BlogManageController::class, 'index'])->name('blogs.index');
        Route::get('blogs/create', [BlogManageController::class, 'create'])->name('blogs.create');
        Route::post('blogs', [BlogManageController::class, 'store'])->name('blogs.store');
        Route::get('blogs/{blog}/edit', [BlogManageController::class, 'edit'])->name('blogs.edit');
        Route::put('blogs/{blog}', [BlogManageController::class, 'update'])->name('blogs.update');
        Route::delete('blogs/{blog}', [BlogManageController::class, 'destroy'])->name('blogs.destroy');
    });

    Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
        Route::post('impersonation/stop', [ImpersonationController::class, 'stop'])->name('impersonation.stop');
    });
});
