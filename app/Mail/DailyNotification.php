<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class DailyNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var User
     */
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Task Update Reminder - Tester Report',
            tags: ['daily-notification', 'reminder'],
            metadata: [
                'user_id' => $this->user->id,
            ],
            replyTo: [
                env('MAIL_REPLY_TO_ADDRESS', 'info@qaadvance.com'),
            ],
        );
    }

    /**
     * Get the message headers.
     */
    public function headers(): Headers
    {
        // Generate a unique message ID with your domain
        $messageId = now()->timestamp . '.' . uniqid() . '@qaadvance.com';

        return new Headers(
            messageId: $messageId,
            references: [],
            text: [
                'X-Priority' => '3', // Normal priority
                'X-Mailer' => 'Tester Report System',
                'List-Unsubscribe' => '<' . URL::to('/unsubscribe/' . $this->user->id) . '>',
                'Precedence' => 'bulk',
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-notification',
            text: 'emails.daily-notification-text',
            with: [
                'userName' => $this->user->name,
                'reportingUrl' => URL::to('/reporting'),
                'unsubscribeUrl' => URL::to('/unsubscribe/' . $this->user->id),
                'currentYear' => date('Y'),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
