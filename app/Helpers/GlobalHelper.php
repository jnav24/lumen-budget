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
    public static function generateToken(int $length = 40): string
    {
        return hash_hmac('sha256', Str::random($length), env('APP_KEY', '$ySt3MOfTh3D0wn!22'));
    }

    /**
     * Generate a random non-hash string
     *
     * @param int $length
     * @return string
     */
    public static function generateSecret(int $length = 6): string
    {
        return strtoupper(Str::random($length));
    }

    /**
     * Send Email
     *
     * @param string $to
     * @param Mailable $mailable
     * @throws \Exception
     */
    public static function sendMailable($to, $mailable)
    {
        try {
            Mail::to($to)->send($mailable);
            Log::info('Sent ' . get_class($mailable) . ' to ' . $to);
        } catch (\Exception $e) {
            Log::info('Failed to send ' . get_class($mailable) . ' to ' . $to . ' error ' . $e->getMessage());
            throw new \Exception('Unable to send email at this time.');
        }
    }
}