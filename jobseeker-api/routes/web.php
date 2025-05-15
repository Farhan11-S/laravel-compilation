<?php

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Notifications\CandidateReviewReminder;
use App\Notifications\CandidateStatusUpdated;
use App\Notifications\CreateJobReminder;
use App\Notifications\GreetingFromAdmin;
use App\Notifications\JobCreatedNotification;
use App\Notifications\JobsNewsletter;
use App\Notifications\PasswordResetNotification;
use App\Notifications\ResumeUploadReminder;
use App\Notifications\PostApplyNotification;
use App\Notifications\PostRegisterCongratulary;
use App\Notifications\SubjectMessageNotification;
use App\Notifications\UserApplied;
use App\Services\UserService;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::get('/preview-email/{identifier}', function (string $identifier) {
    $userService = new UserService();
    $user = $userService->userMock();
    $candidate = $userService->candidateMock();
    $job = $userService->jobMock();

    switch ($identifier) {
        case 'user-applied':
            return (new UserApplied($job, $user, $candidate, $user->company))
                ->toMail($user);
        case 'candidate-status-updated':
            $candidate = Candidate::firstWhere('status', CandidateStatus::ACCEPTED) ?? $candidate;
            return (new CandidateStatusUpdated($candidate, $job, $user, $user->company))
                ->toMail($user);
        case 'post-register-congratulary':
            return (new PostRegisterCongratulary())
                ->toMail($user);
        case 'post-apply-notification':
            return (new PostApplyNotification($job, $user))
                ->toMail($user);
        case 'job-created-notification':
            return (new JobCreatedNotification())
                ->toMail($user);
        case 'greeting-from-admin':
            return (new GreetingFromAdmin($user))
                ->toMail($user);
        case 'subject-message-notification':
            return (new SubjectMessageNotification('subject', '<p>Deskripsi dari sample data</p><p>Text</p><p>Text</p>', '1726035691.png'))
                ->toMail($user);
        case 'jobs-newsletter':
            return (new JobsNewsletter(collect([$job]), $user))
                ->toMail($user);
        case 'resume-upload-reminder':
            return (new ResumeUploadReminder())
                ->toMail($user);
        case 'password-reset-notification':
            return (new PasswordResetNotification($user))
                ->toMail($user);
        case 'candidate-review-reminder':
            return (new CandidateReviewReminder())
                ->toMail($user);
        case 'create-job-reminder':
            return (new CreateJobReminder())
                ->toMail($user);

        default:
            abort(404);
            return;
    }
});

require __DIR__ . '/auth.php';