<?php

use App\Http\Controllers\AccountSettingController;
use App\Http\Controllers\AdminJobController;
use App\Http\Controllers\AdvertisementController;
use App\Http\Controllers\AutosaveResumeController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CSVController;
use App\Http\Controllers\DynamicPageController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InterviewScheduleController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobSeekerController;
use App\Http\Controllers\MassActionController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\SearchResumeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SubscriptionBankController;
use App\Http\Controllers\SubscriptionItemController;
use App\Http\Controllers\SubscriptionScheduleController;
use App\Http\Controllers\SubscriptionTransactionController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\XenditController;
use App\Http\Requests\StoreJob\StepOneRequest;
use App\Http\Requests\StoreJob\StepTwoRequest;
use App\Http\Requests\StoreJob\StepFiveRequest;
use App\Http\Requests\StoreJob\StepFourRequest;
use App\Http\Requests\StoreJob\StepThreeRequest;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminAnalyticController;
use App\Http\Controllers\CandidateAttachmentController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubscriptionLevelController;
use Spatie\Permission\Models\Permission;

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

// GUEST
Route::get('/welcome', [GuestController::class, 'welcome'])->name('welcome');
Route::get('/search-company', [GuestController::class, 'searchCompany'])->name('searchCompany');
Route::get('/viewjob/{id}', [GuestController::class, 'viewjob'])->name('viewjob');
Route::get('/get-website-data', [GuestController::class, 'getWebsiteData'])->name('getWebsiteData');
Route::get('/view-cv-public/{id}', [GuestController::class, 'viewCVPublic'])->name('viewCVPublic');
Route::get('/get-blog-list', [GuestController::class, 'getBlogList'])->name('getBlogList');
Route::get('/view-blog/{id}', [GuestController::class, 'viewBlog'])->name('viewBlog');
Route::get('/get-popular-tags', [GuestController::class, 'getPopularTags'])->name('getPopularTags');
Route::get('/get-faq-pricing', [GuestController::class, 'getFAQPricing'])->name('getFAQPricing');
Route::post('/upload-image', [GuestController::class, 'uploadImage'])->name('uploadImage');
Route::post('/delete-image', [GuestController::class, 'deleteImage'])->name('deleteImage');
Route::get('/get-all-location', [GuestController::class, 'getAllLocationInJobs'])->name('getAllLocationInJobs');

Route::resource('companies', CompanyController::class)->only('index', 'show');
Route::resource('complaints', ComplaintController::class)->only('store');
Route::resource('advertisements', AdvertisementController::class)->only('index');
Route::resource('dynamic-pages', DynamicPageController::class)
    ->parameters([
        'dynamic-pages' => 'link'
    ])
    ->only('index', 'show');
Route::resource('categories', CategoryController::class)->only('index');
Route::prefix('menus')->group(function () {
    Route::get('/get-menus/{place}', [MenuController::class, 'getMenusByPlace']);
    Route::get('/get-menus', [MenuController::class, 'getMenus']);
    Route::get('/get-menus-slug', [MenuController::class, 'getAllMenusSlug']);
});

// AUTH
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    $query = $request->query();
    $showPhone = $query['showPhone'] ?? false;
    $showPasswordIndicator = $query['showPasswordIndicator'] ?? false;
    $user = User::with([
        'roles',
        'permissions',
        'resume',
        'company',
        'unresolvedSubscriptionTransactions',
        'unresolvedSubscriptionTransactions.subscriptionItem',
        'unresolvedSubscriptionTransactions.subscriptionBank',
        'unresolvedSubscriptionTransactions.subscriptionBank.bank',
        'unresolvedSubscriptionTransactions.subscriptionItem.subscriptionSchedule',
    ])
        ->withCount(['notifications' => function (Builder $query) {
            $query->whereNull('read_at');
        }])->find(auth()->id());

    if ($showPasswordIndicator) {
        $user->makeVisible('password');
        $user->password = !empty($user->password);
    }
    $user->makeVisibleIf($showPhone, ['phone']);
    $user->append(['permission', 'jobPostedThisMonthCount', 'interviewPostedThisMonthCount']);
    if ($user->hasRole('employer')) {
        $user->append('employerPosition');
    }

    return $user;
});

// JOB SEEKER
Route::middleware('auth:sanctum')->group(function () {
    Route::resource('candidates', CandidateController::class)->only('store', 'destroy');
    Route::resource('interview-schedules', InterviewScheduleController::class)->only('store', 'show', 'update');
    Route::resource('resumes', ResumeController::class)->only('store', 'index', 'update', 'destroy');
    Route::resource('companies', CompanyController::class)->only('store', 'update');
    Route::resource('notifications', NotificationController::class)->only('index', 'store');
    Route::resource('blogs', BlogController::class);
    Route::resource('tags', TagController::class)->only('index', 'store');
    Route::resource('candidate-attachments', CandidateAttachmentController::class)->only('update');
    Route::post('/generate-linking-ui', [XenditController::class, 'generateLinkingUI']);

    Route::prefix('transactions')->group(function () {
        Route::resource('bank', BankController::class)->only('index', 'show');
        Route::resource('subscription-banks', SubscriptionBankController::class)->only('index', 'show');
        Route::resource('subscription-items', SubscriptionItemController::class)->only('index', 'show');
        Route::get('/get-unresolved-transaction', [SubscriptionTransactionController::class, 'getUserUnresolvedTransaction']);
        Route::patch('/upload-proof-of-payment/{id}', [SubscriptionTransactionController::class, 'uploadProofOfPayment']);
        Route::post('/create-transaction', [SubscriptionTransactionController::class, 'createTransaction']);
    });

    Route::prefix('validations')->group(function () {
        Route::prefix('resumes')->group(function () {
            Route::post('/education', [AutosaveResumeController::class, 'autosaveEducation']);
            Route::post('/work-experience', [AutosaveResumeController::class, 'autosaveWorkExperience']);
            Route::post('/certification', [AutosaveResumeController::class, 'autosaveCertification']);
            Route::post('/user_detail', [AutosaveResumeController::class, 'autosaveUserDetail']);
            Route::post('/language_skill', [AutosaveResumeController::class, 'autosaveLanguageSkill']);
        });
    });

    Route::prefix('job-seeker')->group(function () {
        Route::get('/user-applied-check/{job_id}', [JobSeekerController::class, 'userAppliedCheck']);
        Route::get('/my-job-list', [JobSeekerController::class, 'myJobList']);
    });

    Route::prefix('settings')->group(function () {
        Route::prefix('account')->group(function () {
            Route::patch('/update-role', [AccountSettingController::class, 'updateAccountRole']);
            Route::patch('/update-email', [AccountSettingController::class, 'updateAccountEmail']);
            Route::patch('/update-password', [AccountSettingController::class, 'updateAccountPassword']);
            Route::patch('/update-phone', [AccountSettingController::class, 'updateAccountPhone']);
            Route::patch('/update-employer-position', [AccountSettingController::class, 'updateEmployerPosition']);
            Route::post('/store-password', [AccountSettingController::class, 'storeAccountPassword']);
            Route::post('/store-avatar', [AvatarController::class, 'store']);
        });
    });

    Route::prefix('validations')->group(function () {
        Route::prefix('post-job')->group(function () {
            Route::post('/step-one', fn(StepOneRequest $request) => $request->validated());
            Route::post('/step-two', fn(StepTwoRequest $request) => $request->validated());
            Route::post('/step-three', fn(StepThreeRequest $request) => $request->validated());
            Route::post('/step-four', fn(StepFourRequest $request) => $request->validated());
            Route::post('/step-five', fn(StepFiveRequest $request) => $request->validated());
        });
    });

    Route::prefix('export')->group(function () {
        Route::get('/blog', [CSVController::class, 'blogExportHeader']);
    });
    Route::prefix('import')->group(function () {
        Route::post('/blog', [CSVController::class, 'blogImport']);
    });

    Route::get('/show-all-permission', fn() => ['data' => Permission::get()->pluck('name')->toArray()]);
});

// EMPLOYER
Route::middleware(['auth:sanctum', 'can:access employer dashboard'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/company-data', [DashboardController::class, 'analyticDataCompany']);
        Route::get('/other-data', [DashboardController::class, 'analyticDataEmployer']);
    });
    Route::resource('jobs', JobController::class)->except('create', 'destroy');
    Route::resource('candidates', CandidateController::class)->only('index', 'edit', 'update');
    Route::resource('candidate-attachments', CandidateAttachmentController::class)->only('store');
    Route::resource('resumes', ResumeController::class)->only('show');
    Route::prefix('export')->group(function () {
        Route::get('/job', [CSVController::class, 'jobExportHeader']);
    });
    Route::prefix('import')->group(function () {
        Route::post('/job', [CSVController::class, 'jobImport']);
    });
    Route::prefix('search-resume')->group(function () {
        Route::get('/', [SearchResumeController::class, 'index']);
    });

    Route::prefix('mass-action')->group(function () {
        Route::post('delete/{table}', [MassActionController::class, 'massDelete']);
        Route::post('restore/{table}', [MassActionController::class, 'massRestore']);
    });
});

// ADMIN
Route::prefix('admin')->middleware(['auth:sanctum', 'can:access admin dashboard'])->group(function () {
    Route::resource('advertisements', AdvertisementController::class)->only('store', 'destroy');
    Route::resource('settings', SettingController::class)->only('index', 'store', 'destroy');
    Route::resource('banks', BankController::class)->except('destroy');
    Route::resource('dynamic-pages', DynamicPageController::class)->except('show');
    Route::resource('users', UserController::class)->except('destroy');
    Route::resource('email-templates', EmailTemplateController::class)->only('index', 'edit', 'update');
    Route::resource('subscription-items', SubscriptionItemController::class)->except('destroy', 'store');
    Route::resource('subscription-schedules', SubscriptionScheduleController::class)->only('index', 'edit', 'update');
    Route::resource('subscription-banks', SubscriptionBankController::class);
    Route::resource('subscription-levels', SubscriptionLevelController::class)->only('index', 'update', 'show');
    Route::resource('jobs', AdminJobController::class);
    Route::resource('blogs', BlogController::class);
    Route::resource('tags', TagController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('coupons', CouponController::class);
    Route::resource('companies', CompanyController::class)->except('destroy');
    Route::resource('roles', RoleController::class);
    Route::resource('menus', MenuController::class);
    Route::resource('complaints', ComplaintController::class)->only('index', 'show');
    Route::resource('commands', CommandController::class)->only('index', 'update', 'show');

    Route::prefix('dashboard')->group(function () {
        Route::get('/company-data', [DashboardController::class, 'analyticDataCompany']);
        Route::get('/other-data', [DashboardController::class, 'analyticDataAdmin']);
    });

    Route::prefix('subscription-transaction')->group(function () {
        Route::get('/get-list', [SubscriptionTransactionController::class, 'getListTransaction']);
        Route::get('/get-transaction/{id}', [SubscriptionTransactionController::class, 'getTransactionStatusByID']);
        Route::patch('/update-transaction-status/{id}', [SubscriptionTransactionController::class, 'updateTransactionStatus']);
    });

    Route::prefix('mass-action')->group(function () {
        Route::post('delete/{table}', [MassActionController::class, 'massDelete']);
        Route::post('restore/{table}', [MassActionController::class, 'massRestore']);
        Route::post('send-email', [MassActionController::class, 'massSendEmail']);
    });

    Route::prefix('export')->group(function () {
        Route::get('/job', [CSVController::class, 'jobExportHeader']);
        Route::get('/user', [CSVController::class, 'userExportHeader']);
        Route::get('/blog', [CSVController::class, 'blogExportHeader']);
    });

    Route::prefix('import')->group(function () {
        Route::post('/job', [CSVController::class, 'jobImport']);
        Route::post('/user', [CSVController::class, 'userImport']);
        Route::post('/blog', [CSVController::class, 'blogImport']);
    });

    Route::prefix('users')->group(function () {
        Route::post('/toggle-subscribe-email/{id}', [UserController::class, 'toggleSubscribeEmail']);
    });

    Route::prefix('jobs')->group(function () {
        Route::post('/toggle-premium/{id}', [AdminJobController::class, 'toggleJobPremium']);
    });

    Route::get('/google-analytics', [AdminAnalyticController::class, 'query']);
    Route::get('/google-analytics/v2', [AdminAnalyticController::class, 'compareThisMonthAndLastMonth']);
});

// XENDIT
Route::prefix('xendit')->group(function () {
    Route::post('/recurring', [XenditController::class, 'index']);
    Route::get('/subscription-items', [XenditController::class, 'getSubscriptionItems']);
});
