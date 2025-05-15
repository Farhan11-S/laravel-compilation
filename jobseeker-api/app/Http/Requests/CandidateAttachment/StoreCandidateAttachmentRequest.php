<?php

namespace App\Http\Requests\CandidateAttachment;

use App\Enums\CandidateAttachmentType;
use App\Http\Requests\Boilerplate\EmployerFormRequest;
use Illuminate\Validation\Rule;

class StoreCandidateAttachmentRequest extends EmployerFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'content_link' => 'required|string|max:255|url',
            'content_type' => [
                'required',
                'string',
                'max:255',
                Rule::enum(CandidateAttachmentType::class)
            ],
            'candidate_id' => 'required|exists:candidates,id',
            'notifiable_id' => 'required|exists:users,id',
        ];
    }
}
