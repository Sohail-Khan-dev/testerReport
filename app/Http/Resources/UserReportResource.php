<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->whenLoaded('user', function () {
                return $this->user->name;
            }),
            'project_id' => $this->project_id,
            'project_name' => $this->whenLoaded('project', function () {
                return $this->project->name;
            }),
            'date' => $this->date,
            'task_tested' => $this->task_tested,
            'bug_reported' => $this->bug_reported,
            'regression' => (bool) $this->regression,
            'smoke_testing' => (bool) $this->smoke_testing,
            'client_meeting' => (bool) $this->client_meeting,
            'daily_meeting' => (bool) $this->daily_meeting,
            'mobile_testing' => (bool) $this->mobile_testing,
            'automation' => (bool) $this->automation,
            'other' => $this->other,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
