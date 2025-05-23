<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
        
        // If updating a project, add the ID validation
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['id'] = 'required|exists:projects,id';
        }
        
        // If assigning users to a project
        if ($this->has('user_ids')) {
            $rules['user_ids'] = 'sometimes|array';
            $rules['user_ids.*'] = 'exists:users,id';
        }
        
        return $rules;
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Project Name',
            'description' => 'Project Description',
            'user_ids' => 'Users',
            'user_ids.*' => 'User',
        ];
    }
}
