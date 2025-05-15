<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Helper\Helper;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Company;
use App\Models\SubscriberJob;
use App\Models\User;
use App\Services\AvatarService;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use NunoMaduro\Collision\Adapters\Phpunit\Subscribers\Subscriber;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $limit = $query['limit'] ?? 0;
        $search = $query['search'] ?? '';
        $sort_by = $query['sort_by'] ?? 'updated_at';
        $order = $query['order'] ?? 'desc';
        $role_id = $query['role_id'] ?? '';
        $package_type = $query['package_type'] ?? '';
        $company_id = $query['company_id'] ?? '';
        $provider = $query['provider'] ?? '';
        $trashed = $query['trashed'] ?? false;
        $filterPreset = $query['filter_preset'] ?? null;

        $query = User::when(
            $search,
            function (Builder $query, string $search) {
                $query->where(
                    function (Builder $query) use ($search) {
                        $query->where('name', 'like', '%' . strtolower($search) . '%')
                            ->orWhere('email', 'like', '%' . strtolower($search) . '%');
                    }
                );
            }
        )
            ->when(
                $trashed,
                function (Builder $query) {
                    $query->onlyTrashed();
                }
            )
            ->when(
                $role_id,
                function (Builder $query, $role_id) {
                    $query->where('role_id', $role_id);

                    if ($role_id == Roles::EMPLOYER) {
                        $query->withCount('jobs');
                    } else if ($role_id == Roles::JOB_SEEKER) {
                        $query->withCount(['candidates' => function (Builder $query) {
                            $query->whereNot('status', 'saved');
                        }]);
                    }
                }
            )
            ->when(
                $company_id,
                function (Builder $query, $company_id) {
                    $query->where('company_id', $company_id);
                }
            )
            ->when(
                $package_type,
                function (Builder $query, $package_type) {
                    $query->where('package_type', $package_type);
                }
            )
            ->when(
                $provider,
                function (Builder $query, $provider) {
                    if ($provider == 'manual') {
                        $query->doesntHave('providers');
                        return;
                    }
                    $query->whereHas(
                        'providers',
                        function (Builder $query) use ($provider) {
                            $query->where('provider', $provider);
                        }
                    );
                },
            )
            ->with(['deletedBy', 'company', 'providers'])
            ->with(['subscriberJob' => function ($query) {
                $query->select('id', 'status', 'user_id');
            }])
            ->orderByRaw($sort_by . ' ' . $order);

        Helper::filterPreset($query, $filterPreset);

        $result = $query->get();
        if ($limit > 0) {
            $result = $query->paginate($limit);
        }

        return $result;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        DB::transaction(function () use ($request) {
            $validated = $request->validated();
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role_id' => $validated['role_id'],
            ]);

            $role = Role::findById($validated['role_id'], 'web');
            $user->assignRole($role->name);


            if ($validated['role_id'] == Roles::EMPLOYER) {
                $company = Company::updateOrCreate([
                    'name' =>  $validated['company_name']
                ], [
                    'industry' => $validated['company_industry']  ?? null,
                ]);

                $user['company_id'] = $company->id;
                $user->save();
            }

            $user->providers()->create([
                'provider' => 'admin',
                'provider_id' => $user->id,
            ]);

            event(new Registered($user));
        });

        return [
            'success' => true,
            'message' => 'Berhasil membuat user baru!'
        ];
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $avatarService = new AvatarService();
        $user->makeVisible(['phone']);

        if (!empty($user)) $user = $avatarService->getUserWithAvatar($user);

        $user->load([
            'company',
            'resume.educations',
            'resume.certifications',
            'resume.language_skills',
            'resume.skills',
            'resume.user_detail',
            'resume.work_experiences',

        ]);
        return [
            'success' => true,
            'message' => 'Berhasil menampilkan detail user!',
            'data' => $user
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->makeVisible(['phone']);
        $user->load([
            'company'
        ]);
        return [
            'success' => true,
            'message' => 'Berhasil menampilkan detail user!',
            'data' => $user
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        DB::transaction(function () use ($request, $user,) {
            $validated = $request->validated();
            if ($user->email == 'admin@mail.com') $validated['role_id'] = Roles::SUPERADMIN;

            if (!empty($validated['phone'])) $validated['phone'] = $this->userService->formatPhoneNumber($validated['phone']);
            if ($request->role_id == Roles::EMPLOYER) {
                $company = Company::updateOrCreate([
                    'name' => $validated['company_name'],
                ], [
                    'industry' => $validated['company_industry'],
                ]);
                $validated['company_id'] = $company->id;
            } else {
                $user->company_id = null;
                $user->save();
            }

            $user->syncRoles([]);
            $role = Role::findById($validated['role_id'], 'web');
            $user->assignRole($role->name);

            $user->update($validated);
        });

        return [
            'success' => true,
            'message' => 'Berhasil mengubah detail user!'
        ];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    public function toggleSubscribeEmail(string $id)
    {
        $subs = SubscriberJob::where('user_id', $id)->firstOrFail();

        if ($subs->status == 'active') {
            $subs->status = 'inactive';
        } else {
            $subs->status = 'active';
        }

        $subs->save();

        return [
            'success' => true,
            'message' => 'Berhasil merubah status berlangganan email!',
            'data' => $subs
        ];
    }
}
