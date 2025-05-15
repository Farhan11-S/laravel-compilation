<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\Boilerplate\SuperadminFormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class UpdateRoleRequest extends SuperadminFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $permissions = Permission::all()->pluck('name')->toArray();
        return [
            'name' => 'nullable|min:4',
            'permissions.*' => Rule::in($permissions),
        ];
    }
}
