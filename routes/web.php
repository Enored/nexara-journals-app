<?php

use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\AnnouncementManageController;
use App\Http\Controllers\Admin\BlogManageController;
use App\Http\Controllers\Admin\PlatformSettingsController;
use App\Http\Controllers\Admin\JournalEditionManageController;
use App\Http\Controllers\Admin\JournalManageController;
use App\Models\Edition;
use App\Models\Journal;
use App\Http\Controllers\Admin\UserManageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthorRevisionController;
use App\Http\Controllers\Dashboard\AuthorSubmissionController;
use App\Http\Controllers\Dashboard\AuthorSubmissionShowController;
use App\Http\Controllers\Dashboard\EditorJournalController;
use App\Http\Controllers\Dashboard\EditorPipelineController;
use App\Http\Controllers\Dashboard\EditorSubmissionShowController;
use App\Http\Controllers\Dashboard\ReviewerInboxController;
use App\Http\Controllers\Dashboard\RoleDashboardController;
use App\Http\Controllers\DashboardHubController;
use App\Http\Controllers\EditorDecisionController;
use App\Http\Controllers\EditorSubmissionActionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicArticlesDirectoryController;
use App\Http\Controllers\PublicBlogController;
use App\Http\Controllers\PublicJournalsController;
use App\Http\Controllers\JournalSubmissionWebController;
use App\Http\Controllers\PublicArticleController;
use App\Http\Controllers\ReviewerTaskController;
use App\Http\Controllers\SubmissionFileDownloadController;
use App\Http\Controllers\SubmissionLegacyRedirectController;
use App\Http\Controllers\SubmissionPublishController;
use App\Http\Controllers\UserSettingsController;
use App\Support\AboutPayload;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/journals', [PublicJournalsController::class, 'index'])->name('journals.index');
Route::get('/articles', [PublicArticlesDirectoryController::class, 'index'])->name('articles.index');
Route::get('/blogs', [PublicBlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{slug}', [PublicBlogController::class, 'show'])->name('blogs.show');
Route::get('/about', fn () => Inertia::render('Platform/About', AboutPayload::build()))->name('about');
Route::get('/articles/{submission}', [PublicArticleController::class, 'show'])->name('journal.articles.show');


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
    Route::get('/author/submissions', [AuthorSubmissionController::class, 'index'])->name('author.submissions');
    Route::post('/author/submissions', [AuthorSubmissionController::class, 'store'])->name('author.submissions.store');
    Route::get('/author/submissions/{submission}', [AuthorSubmissionShowController::class, 'show'])->name('author.submissions.show');
    Route::post('/author/submissions/{submission}/revision', [AuthorRevisionController::class, 'store'])->name('author.submissions.revision.store');

    Route::get('/editor/dashboard', [RoleDashboardController::class, 'editor'])->name('editor.dashboard');
    Route::get('/editor/journals', [EditorJournalController::class, 'index'])->name('editor.journals.index');
    Route::get('/editor/submissions', EditorPipelineController::class)->name('editor.submissions');
    Route::get('/editor/pipeline', fn () => redirect()->route('editor.submissions', absolute: false), 301);

    Route::get('admin/journals/{journal}/editions', function (Journal $journal) {
        return redirect()->route('journal.editions.index', $journal, absolute: false, status: 301);
    })->name('admin.journals.editions.legacy-index');
    Route::get('admin/journals/{journal}/editions/{edition}', function (Journal $journal, Edition $edition) {
        return redirect()->route('journal.editions.show', [$journal, $edition], absolute: false, status: 301);
    });

    Route::prefix('journals/{journal}')->name('journal.')->group(function () {
        Route::get('editions', [JournalEditionManageController::class, 'index'])->name('editions.index');
        Route::get('volumes/create', [JournalEditionManageController::class, 'createVolume'])->name('volumes.create');
        Route::post('volumes', [JournalEditionManageController::class, 'storeVolume'])->name('volumes.store');
        Route::delete('volumes/{volume}', [JournalEditionManageController::class, 'destroyVolume'])->name('volumes.destroy');
        Route::get('editions/create', [JournalEditionManageController::class, 'create'])->name('editions.create');
        Route::post('editions', [JournalEditionManageController::class, 'store'])->name('editions.store');
        Route::get('editions/{edition}', [JournalEditionManageController::class, 'show'])->name('editions.show');
        Route::get('editions/{edition}/edit', [JournalEditionManageController::class, 'edit'])->name('editions.edit');
        Route::get('editions/{edition}/publish', [JournalEditionManageController::class, 'publishForm'])->name('editions.publish-form');
        Route::get('editions/{edition}/articles/add', [JournalEditionManageController::class, 'addArticleForm'])->name('editions.articles.add-form');
        Route::put('editions/{edition}', [JournalEditionManageController::class, 'update'])->name('editions.update');
        Route::delete('editions/{edition}', [JournalEditionManageController::class, 'destroy'])->name('editions.destroy');
        Route::post('editions/{edition}/publish', [JournalEditionManageController::class, 'publishIssue'])->name('editions.publish');
        Route::post('editions/{edition}/unpublish', [JournalEditionManageController::class, 'unpublishIssue'])->name('editions.unpublish');
        Route::post('editions/{edition}/articles', [JournalEditionManageController::class, 'assignArticle'])->name('editions.articles.assign');
        Route::delete('editions/{edition}/articles/{submission}', [JournalEditionManageController::class, 'removeArticle'])->name('editions.articles.remove');
    });
    Route::get('/editor/submissions/{submission}', [EditorSubmissionShowController::class, 'show'])->name('editor.submissions.show');
    Route::post('/editor/submissions/{submission}/assign-reviewer', [EditorSubmissionActionController::class, 'assignReviewer'])->name('editor.submissions.assign-reviewer');
    Route::post('/editor/submissions/{submission}/send-for-review', [EditorSubmissionActionController::class, 'sendForReview'])->name('editor.submissions.send-for-review');
    Route::post('/editor/submissions/{submission}/return-to-author', [EditorSubmissionActionController::class, 'returnToAuthor'])->name('editor.submissions.return-to-author');
    Route::post('/editor/submissions/{submission}/desk-reject', [EditorSubmissionActionController::class, 'deskReject'])->name('editor.submissions.desk-reject');
    Route::post('/editor/submissions/{submission}/decision', [EditorDecisionController::class, 'store'])->name('editor.submissions.decision');
    Route::post('/editor/submissions/{submission}/publish', [SubmissionPublishController::class, 'store'])->name('editor.submissions.publish');

    Route::get('/reviewer/dashboard', [RoleDashboardController::class, 'reviewer'])->name('reviewer.dashboard');
    Route::get('/reviewer/inbox', ReviewerInboxController::class)->name('reviewer.inbox');

    Route::get('/dashboard/author', fn () => redirect()->away(platform_route('author.submissions'), 301));
    Route::get('/dashboard/editor', fn () => redirect()->away(platform_route('editor.submissions'), 301));
    Route::get('/dashboard/reviewer', fn () => redirect()->away(platform_route('reviewer.inbox'), 301));
    Route::get('/dashboard/admin', fn () => redirect()->away(platform_route('admin.dashboard'), 301));

    Route::get('/submissions/{submission}', SubmissionLegacyRedirectController::class)->name('submissions.show');

    Route::get('/review-tasks/{assignment}', [ReviewerTaskController::class, 'show'])->name('review-tasks.show');
    Route::post('/review-tasks/{assignment}', [ReviewerTaskController::class, 'store'])->name('review-tasks.store');

    Route::get('/submission-files/{file}', [SubmissionFileDownloadController::class, 'show'])->name('submission-files.download');

    Route::middleware('platform.admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [RoleDashboardController::class, 'admin'])->name('dashboard');

        Route::get('journals', [JournalManageController::class, 'index'])->name('journals.index');
        Route::get('journals/create', [JournalManageController::class, 'create'])->name('journals.create');
        Route::post('journals', [JournalManageController::class, 'store'])->name('journals.store');
        Route::get('journals/{journal}/edit', [JournalManageController::class, 'edit'])->name('journals.edit');
        Route::put('journals/{journal}', [JournalManageController::class, 'update'])->name('journals.update');

        Route::get('users/export', [UserManageController::class, 'export'])->name('users.export');
        Route::get('users', [UserManageController::class, 'index'])->name('users.index');
        Route::post('users', [UserManageController::class, 'store'])->name('users.store');
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

        Route::get('announcements', [AnnouncementManageController::class, 'index'])->name('announcements.index');
        Route::post('announcements', [AnnouncementManageController::class, 'store'])->name('announcements.store');
        Route::put('announcements/{announcement}', [AnnouncementManageController::class, 'update'])->name('announcements.update');
        Route::delete('announcements/{announcement}', [AnnouncementManageController::class, 'destroy'])->name('announcements.destroy');

        Route::get('settings', [PlatformSettingsController::class, 'edit'])->name('settings.edit');
        Route::put('settings/branding', [PlatformSettingsController::class, 'updateBranding'])->name('settings.branding.update');
        Route::put('settings/general', [PlatformSettingsController::class, 'updateGeneral'])->name('settings.general.update');
    });

    Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
        Route::post('impersonation/stop', [ImpersonationController::class, 'stop'])->name('impersonation.stop');
    });
});
