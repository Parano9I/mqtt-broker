<?php

namespace App\Http\Requests\Sensor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
            'name'        => [
                'required',
                'string',
                'between:5,150',
                Rule::unique('sensors')->where(function ($query) {
                    $query->where('name', $this->name)
                        ->where('topic_id', $this->topic);
                })
            ],
            'description' => 'required|string|min:30'
        ];
    }
}
