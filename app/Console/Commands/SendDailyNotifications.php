<?php

namespace App\Console\Commands;

use App\Mail\DailyNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SendDailyNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily notification emails to all registered users at 5:20 PM Pakistan time (8:20 AM EDT)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if the email_notifications column exists, if not, add it
        if (!Schema::hasColumn('users', 'email_notifications')) {
            $this->info("Adding email_notifications column to users table...");
            Schema::table('users', function ($table) {
                $table->boolean('email_notifications')->default(true);
            });
            $this->info("Column added successfully.");
        }

        // Get only users who have not opted out of email notifications
        $users = User::where(function($query) {
            $query->where('email_notifications', true)
                  ->orWhereNull('email_notifications');
        })->get();

        $count = 0;

        $this->info("Starting to send daily notification emails...");

        foreach ($users as $user) {
            try {
                // Add a small delay between emails to avoid triggering spam filters
                if ($count > 0) {
                    sleep(1); // 1 second delay between emails
                }

                Mail::to($user->email)->send(new DailyNotification($user));
                $count++;
                $this->info("Email sent to: {$user->email}");
            } catch (\Exception $e) {
                $this->error("Failed to send email to {$user->email}: {$e->getMessage()}");
                Log::error("Failed to send daily notification email to {$user->email}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Count users who have opted out
        $optedOut = User::where('email_notifications', false)->count();

        $this->info("Daily notification emails sent to {$count} users.");
        $this->info("{$optedOut} users have opted out of email notifications.");

        return Command::SUCCESS;
    }
}
