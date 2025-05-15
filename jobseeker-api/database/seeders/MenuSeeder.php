<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class MenuSeeder extends Seeder
{
    private function createMenuPermissions($slug)
    {
        $permissionList =  Permission::firstOrCreate(['name' => 'access ' . $slug . ' list'], ['guard_name' => 'web']);
        $permissionDetail =  Permission::firstOrCreate(['name' => 'access ' . $slug . ' detail'], ['guard_name' => 'web']);
        $permissionCreate =  Permission::firstOrCreate(['name' => 'create ' . $slug], ['guard_name' => 'web']);
        $permissionUpdate =  Permission::firstOrCreate(['name' => 'update ' . $slug], ['guard_name' => 'web']);
        $permissionDelete =  Permission::firstOrCreate(['name' => 'delete ' . $slug], ['guard_name' => 'web']);

        return [
            $permissionList,
            $permissionDetail,
            $permissionCreate,
            $permissionUpdate,
            $permissionDelete,
        ];
    }

    private function givePermissionsToRole($role, $permissions)
    {
        $role->givePermissionTo($permissions);
    }

    private function createMenu($menuLabel, $slugMenu, $link, $parent, $place, $position)
    {
        return Menu::create(
            [
                'label' => $menuLabel,
                'slug' => $slugMenu,
                'link' => $link,
                'parent' => $parent,
                'place' => $place,
                'can_access_list' => 1,
                'can_access_detail' => 1,
                'can_create' => 1,
                'can_update' => 1,
                'can_delete' => 1,
                'position' => $position,
            ]
        );
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            'admin' => [
                'Dashboard Admin' => [
                    'link' => '/admin/dashboard',
                    'children' => []
                ],
                'Control Panel' => [
                    'children' => [
                        'Control Panel Website',
                        'Control ENV',
                        'Sitemap',
                        'Control Social Media',
                        'Control FAQ',
                    ]
                ],
                'Email Control Panel' => [
                    'children' => []
                ],
                'User Management' => [
                    'children' => []
                ],
                'Role Management' => [
                    'children' => []
                ],
                'Company Management' => [
                    'children' => []
                ],
                'Job Management' => [
                    'children' => []
                ],
                'Upload Ads' => [
                    'children' => []
                ],
                'User Management' => [
                    'children' => [
                        'All Users',
                        'Superadmin Users',
                        'Employer Users',
                        'Job Seeker Users'
                    ]
                ],
                'Dynamic Page' => [
                    'children' => []
                ],
                'Menu Management' => [
                    'children' => []
                ],
                'Blog Management' => [
                    'children' => [
                        'Blogs',
                        'Tags',
                        'Categories'
                    ]
                ],
                'Coupon Management' => [
                    'children' => []
                ],
                'Contact Us Management' => [
                    'children' => [
                        'Contact Us',
                        'Category',
                        'Department'
                    ]
                ],
                'Analytics' => [
                    'children' => []
                ],
                'Role Management' => [
                    'children' => []
                ],
                'Subscriptions' => [
                    'children' => [
                        'Subscription Transactions',
                        'Subscription Banks',
                        'Subscription Items',
                        'Subscription Schedules',
                        'Subscription Levels',
                    ]
                ],
                'Command Management' => [
                    'children' => []
                ],
            ],
            'profile' => [
                'Profile' => [
                    'link' => '/profile',
                    'children' => []
                ],
                'My Jobs' => [
                    'children' => []
                ],
                'Settings' => [
                    'children' => []
                ],
                'My Blogs' => [
                    'children' => []
                ],
            ],
            'dashboard' => [
                'Dashboard' => [
                    'link' => '/dashboard',
                    'children' => []
                ],
                'Jobs' => [
                    'children' => []
                ],
                'Candidates' => [
                    'children' => []
                ],
                'Search Resume' => [
                    'children' => []
                ],
            ],

        ];

        $position = 1;
        foreach ($menus as $place => $placeMenus) {
            $admin = Role::where('name', 'superadmin')->first();
            $jobSeeker = Role::where('name', 'job seeker')->first();
            $employer = Role::where('name', 'employer')->first();

            foreach ($placeMenus as $menuLabel => $menu) {
                $slugMenu = Str::slug($menuLabel);

                $link = '/' . $place . '/' . $slugMenu;
                if (!empty($menu['link'])) {
                    $link = $menu['link'];
                }

                $menuModel = Menu::firstWhere('link', $link);

                if ($isNotYetSeeded = ($menuModel == null)) {
                    $count = Menu::where('slug', 'like', $slugMenu . '-%')->count();
                    if ($count > 0) {
                        $slugMenu = $slugMenu . '-' . ($count + 1);
                    }
                }

                if ($isNotYetSeeded) $menuModel = $this->createMenu($menuLabel, $slugMenu, $link, 0, $place, $position);

                $positionChild = 1;
                foreach ($menu['children'] as $childMenu) {
                    $slugChildMenu = Str::slug($childMenu);

                    if (Menu::where('link', '/' . $place . '/' . $slugMenu . '/' . $slugChildMenu)->exists()) continue;
                    if (Menu::where('slug', $slugChildMenu)->exists()) {
                        $count = Menu::where('slug', 'like', $slugChildMenu . '-%')->count();
                        $slugChildMenu = $slugChildMenu . '-' . ($count + 1);
                    }

                    $this->createMenu($childMenu, $slugChildMenu, '/' . $place . '/' . $slugMenu . '/' . $slugChildMenu, $menuModel->id, $place, $positionChild);

                    $permissionCRUD = $this->createMenuPermissions($slugChildMenu);
                    $this->givePermissionsToRole($admin, $permissionCRUD);

                    if ($place !== 'admin') {
                        $this->givePermissionsToRole($employer, $permissionCRUD);
                    }

                    if ($place === 'profile') {
                        $this->givePermissionsToRole($jobSeeker, $permissionCRUD);
                    }
                    $positionChild++;
                }

                // Skip if already seeded
                if (!$isNotYetSeeded) continue;

                $permissionCRUD = $this->createMenuPermissions($slugMenu);
                $this->givePermissionsToRole($admin, $permissionCRUD);

                if ($place !== 'admin') {
                    $this->givePermissionsToRole($employer, $permissionCRUD);
                }

                if ($place === 'profile') {
                    $this->givePermissionsToRole($jobSeeker, $permissionCRUD);
                }
                $position++;
            }

            // rollback position in different place
            $position = 1;
        }
    }
}
