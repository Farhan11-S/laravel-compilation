<?php

namespace App\Http\Requests\CandidateAttachment;

use App\Http\Requests\Boilerplate\LoggedInFormRequest;

class UpdateCandidateAttachmentRequest extends LoggedInFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_attachments' => 'required|array|max:5',
            'user_attachments.*' => 'required|file',
        ];
    }
}
