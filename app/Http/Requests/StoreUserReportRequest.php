<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserReportRequest extends FormRequest
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
        return [
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'date' => 'required|date',
            'task_tested' => 'nullable|integer|min:0',
            'bug_reported' => 'nullable|integer|min:0',
            'regression' => 'nullable|boolean',
            'smoke_testing' => 'nullable|boolean',
            'client_meeting' => 'nullable|boolean',
            'daily_meeting' => 'nullable|boolean',
            'mobile_testing' => 'nullable|boolean',
            'automation' => 'nullable|boolean',
            'description' => 'nullable|string',
            'other' => 'nullable|string|max:255',
        ];
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'User',
            'project_id' => 'Project',
            'task_tested' => 'Tasks Tested',
            'bug_reported' => 'Bugs Reported',
            'regression' => 'Regression Testing',
            'smoke_testing' => 'Smoke Testing',
            'client_meeting' => 'Client Meeting',
            'daily_meeting' => 'Daily Meeting',
            'mobile_testing' => 'Mobile Testing',
            'automation' => 'Automation Testing',
        ];
    }
    
    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Set default user_id if not provided
        if (!$this->has('user_id') || $this->input('user_id') === null) {
            $this->merge([
                'user_id' => auth()->id(),
            ]);
        }
        
        // Convert checkbox values to boolean
        $checkboxFields = [
            'regression',
            'smoke_testing',
            'client_meeting',
            'daily_meeting',
            'mobile_testing',
            'automation',
        ];
        
        foreach ($checkboxFields as $field) {
            if ($this->has($field)) {
                $this->merge([
                    $field => $this->boolean($field),
                ]);
            }
        }
    }
}
