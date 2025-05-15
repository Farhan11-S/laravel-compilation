<?php

namespace App\Http\Requests;

use App\Enums\CandidateStatus;
use App\Models\Candidate;
use App\Models\Resume;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Candidate::where([
            ['job_id', $this->input('job_id')],
            ['user_id', auth()->user()->id],
        ])
            ->whereNotIn('status', ['saved'])
            ->doesntExist() && Resume::where(
                fn($query) => $query->where('user_id', auth()->user()->id)
                    ->where('is_complete', 1)
            )->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'job_id' => ['required', 'integer', 'exists:jobs,id'],
            'status' => ['required', 'string',  new Enum(CandidateStatus::class)],
        ];
    }
}
