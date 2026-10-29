<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'                   => ['required', 'string', 'max:255'],
            'email'                  => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'username'               => [
                'nullable',
                'string',
                'max:50',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'birthday'               => ['nullable', 'date'],
            'bio'                    => ['nullable', 'string', 'max:1000'],
            'avatar_file'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'avatar'                 => ['nullable', 'string', 'max:500'],
            'theme_preference'       => ['nullable', 'string', Rule::in(['dark', 'light', 'system'])],
            'language'               => ['nullable', 'string', Rule::in(['en', 'es', 'fr', 'de', 'ja'])],
            'date_format'            => ['nullable', 'string', Rule::in(['Y-m-d', 'm/d/Y', 'd/m/Y'])],
            'time_format'            => ['nullable', 'string', Rule::in(['12h', '24h'])],
            'notify_email'           => ['nullable', 'boolean'],
            'notify_tasks'           => ['nullable', 'boolean'],
            'notify_events'          => ['nullable', 'boolean'],
            'default_dashboard_view' => ['nullable', 'string', Rule::in(['overview', 'tasks', 'calendar'])],
            'two_factor_enabled'     => ['nullable', 'boolean'],
            'privacy_visibility'     => ['nullable', 'string', Rule::in(['private', 'public'])],
        ];
    }
}
