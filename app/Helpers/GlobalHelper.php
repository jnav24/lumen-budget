<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Str;

class GlobalHelper
{
    /**
     * Return now if date time is null
     *
     * @param Carbon|null $dateTime
     * @param string $timeZone
     *
     * @return Carbon
     */
    public static function setDefaultDateTimeIfNull($dateTime = null, string $timeZone = 'UTC'): Carbon
    {
        return $dateTime ?? Carbon::now($timeZone);
    }

    /**
     * Generate a random string
     *
     * @param int $length
     * @return string
     */
    public static function generateToken(int $length = 40)
    {
        return hash_hmac('sha256', Str::random($length), env('APP_KEY', '$ySt3MOfTh3D0wn!22'));
    }

    /**
     * Send Email
     *
     * @param string $to
     * @param Mailable $mailable
     * @return bool
     */
    public static function sendMailable($to, $mailable)
    {
        try {
            Mail::to($to)->send($mailable);
            Log::info('Sent ' . get_class($mailable) . ' to ' . $to);
            return true;
        } catch(\Exception $ex) {
            Log::info('Failed to send ' . get_class($mailable) . ' to ' . $to . ' error ' . $ex->getMessage() . ' ' . get_class(new Mail()));
            return false;
        }
    }
}