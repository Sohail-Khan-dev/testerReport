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
                    'user_id' => 4, // Assuming user IDs 4 and  6 only
                    'project_id' => rand(14, 16), // Assuming project IDs 1 , 14, 15,16
                    'date' => $date->copy()->addDays(rand(1, $date->daysInMonth)),
                    'task_tested' =>  rand(1, 20),
                    'bug_reported' =>  rand(1, 20),
                    'other' =>  rand(1, 20),
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