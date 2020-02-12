<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyTokenMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var $user
     */
    public $user;

    public $device;

    /**
     * VerifyTokenMailable constructor.
     * @param User $user
     */
    public function __construct(User $user, $device)
    {
        $this->user = $user;
        $this->device = $device;
    }

    /**
     * Build the email
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('[Dime Budget] Verify Your Account')
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->replyTo(env('MAIL_REPLY_TO_ADDRESS'), env('APP_NAME'))
            ->view('emails.auth.verify');
    }
}