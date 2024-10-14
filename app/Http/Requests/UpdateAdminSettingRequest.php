<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateAdminSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->hasRole(config('constants.role.admin'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'version' => ['required', 'unique:admin_settings,version'],
            'cost_bw_page' => ['required', 'decimal:0,2'],
            'cost_color_page' => ['required', 'decimal:0,2'],
            'cost_pixel_image' => ['required', 'decimal:0,8'],
            'is_preferred' => ['required', 'boolean'],
        ];
    }
}
