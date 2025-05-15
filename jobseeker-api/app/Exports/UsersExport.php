<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user = new User([
            'name' => 'User Name',
            'email' => 'usermail@mail.com',
            'phone' => null,
            'password' => 'sukses2024',
            'role_id' => 2,
        ]);

        $user->makeVisible(['phone', 'password']);
        return collect([$user]);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Password',
            'Role ID',
            'Company Name',
            'Company Industry',
        ];
    }
}
