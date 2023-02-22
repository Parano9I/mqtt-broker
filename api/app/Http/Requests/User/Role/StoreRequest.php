<?php

namespace App\Http\Requests\User\Role;

use App\Rules\NotRolesRule;
use App\Rules\RoleRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'role' => ['required', 'string', RoleRule::class, new NotRolesRule(['owner'])]
        ];
    }
}
