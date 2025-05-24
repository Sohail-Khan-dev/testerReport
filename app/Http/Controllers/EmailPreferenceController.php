<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmailPreferenceController extends Controller
{
    /**
     * Show the unsubscribe page for a user.
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function unsubscribe(User $user)
    {
        // Create a simple view to confirm unsubscribe
        return view('emails.unsubscribe', [
            'user' => $user
        ]);
    }

    /**
     * Update the user's email preferences.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        try {
            // Add a column to users table to track email preferences if it doesn't exist
            if (!DB::connection()->getSchemaBuilder()->hasColumn('users', 'email_notifications')) {
                DB::statement('ALTER TABLE users ADD COLUMN email_notifications BOOLEAN DEFAULT TRUE');
            }

            // Update the user's email preferences
            $user->email_notifications = false;
            $user->save();

            return redirect()->back()->with('success', 'You have been successfully unsubscribed from email notifications.');
        } catch (\Exception $e) {
            Log::error('Failed to update email preferences: ' . $e->getMessage());
            return redirect()->back()->with('error', 'There was an error processing your request. Please try again later.');
        }
    }
}
