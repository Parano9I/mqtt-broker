<?php

namespace App\Http\Requests\Group\User;

use App\Rules\GroupRoleRule;
use App\Rules\NotOwnerGroupRoleRule;
use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'role_id' => ['required', 'int', new GroupRoleRule(), new NotOwnerGroupRoleRule()]
        ];
    }
}
