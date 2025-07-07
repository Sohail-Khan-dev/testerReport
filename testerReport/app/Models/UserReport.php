<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserReport;
use Carbon\Carbon;

class UserReportSeeder extends Seeder
{
    public function run()
    {
        $months = 6;
        $recordsPerMonth = 10;

        for ($i = 0; $i < $months; $i++) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();

            for ($j = 0; $j < $recordsPerMonth; $j++) {
                UserReport::create([
                    'user_id' => rand(1, 50), // Assuming user IDs range from 1 to 50
                    'project_id' => rand(1, 20), // Assuming project IDs range from 1 to 20
                    'date' => $date->copy()->addDays(rand(1, $date->daysInMonth)),
                    'task_tested' => 'Task ' . rand(1, 100),
                    'bug_reported' => 'Bug ' . rand(1, 100),
                    'other' => 'Other ' . rand(1, 100),
                    'regression' => rand(0, 1),
                    'smoke_testing' => rand(0, 1),
                    'client_meeting' => rand(0, 1),
                    'daily_meeting' => rand(0, 1),
                    'mobile_testing' => rand(0, 1),
                    'description' => 'Description for task ' . rand(1, 100),
                    'automation' => rand(0, 1),
                ]);
            }
        }
    }
}