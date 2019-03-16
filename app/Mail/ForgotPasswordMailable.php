<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Helpers\GlobalHelper;

class ForgotPasswordMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var $user
     */
    public $user;

    /**
     * ForgotPasswordMailable constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->user->password_reset_token = GlobalHelper::generateToken(40);
        $this->user->password_reset_expires = GlobalHelper::setDefaultDateTimeIfNull()->addHour();
        $this->user->save();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(env('APP_NAME', 'System') . ' password reset')
            ->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))
            ->replyTo(env('MAIL_REPLY_TO_ADDRESS'), env('APP_NAME'))
            ->subject('Dime Budget Forgot My Password')
            ->view('emails.auth.forgotpassword');
    }
}