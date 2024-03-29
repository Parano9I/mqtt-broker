<?php

namespace App\Http\Requests\Group\User;


use App\Rules\GroupRoleRule;
use App\Rules\NotOwnerGroupRoleRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|int|exists:users,id',
        ];
    }
}
