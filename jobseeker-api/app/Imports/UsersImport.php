<?php

namespace App\Imports;

use App\Constants\Roles;
use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;

class UsersImport implements OnEachRow, WithHeadingRow, WithValidation
{
    use Importable;

    public function onRow(Row $row)
    {
        $row      = $row->toArray();
        $user = User::create([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'phone' => $row['phone'],
            'role_id' => $row['role_id'],
        ]);

        if ($row['role_id'] == Roles::EMPLOYER) {
            $company = Company::firstOrCreate([
                'name' => $row['company_name'],
            ], [
                'industry' => $row['company_industry'],
            ]);

            $user['company_id'] = $company->id;
        }

        $user->providers()->create([
            'provider' => 'admin',
            'provider_id' => $user->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'phone' => ['nullable', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', Password::defaults()],
            'role_id' => ['required', 'integer', 'exists:' . Role::class . ',id'],
            'company_name' => ['required_if:role_id,' . Roles::EMPLOYER, 'nullable', 'string'],
            'company_industry' => ['nullable', 'string'],
        ];
    }
}
