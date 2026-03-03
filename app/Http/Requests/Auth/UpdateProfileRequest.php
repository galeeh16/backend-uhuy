<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        if ($user->role === 'TALENT') {
             return [
                'location'          => 'required|string|max:255',
                'full_address'      => 'required|string|max:255',
                'about_me'          => 'required|string|max:255',
                'phone'             => 'required|string|min:10,max:15',
                'photo'             => 'required|file|mimes:png,jpg,jpeg,webp|max:5120',
                'cv'                => 'required|file|mimes:pdf|max:5120',
                'portfolio'         => 'required|file|mimes:pdf|max:5120',
                'birth_date'        => 'required|string|date_format:Y-m-d',
                'experience_year'   => 'required|integer|gte:0',
                'availability_for_work' => 'required|boolean',
            ];
        }

        if ($user->role === 'COMPANY') {
            return [
                'address'           => 'required|string|max:255',
                'location'          => 'required|string|max:255',
                'about_company'     => 'required|string|max:5000',
                'company_size'      => 'required|integer|gt:0',
                'founded_in'        => 'required|string|date_format:Y-m-d',
                'photo'             => 'required|file|mimes:png,jpg,jpeg,webp|max:5120',
                'webstie_url'       => 'nullable|url',
                'facebook_url'      => 'nullable|url',
                'instagram_url'     => 'nullable|url',
                'twitter_url'       => 'nullable|url',
                'linked_in_url'     => 'nullable|url',
            ];
        }

        return [];
    }
}
