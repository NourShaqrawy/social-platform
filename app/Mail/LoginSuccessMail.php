<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user; // تعريف المتغير هنا

    public function __construct($user)
    {
        $this->user = $user; // تخزينه داخل الكلاس
    }

    public function build()
    {
        return $this->subject('تم تسجيل الدخول بنجاح')
                    ->view('emails.login-success')
                    ->with([
                        'user' => $this->user,
                    ]);
    }
}
