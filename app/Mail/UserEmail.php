<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;

    public $user;

    public function __construct($subject, $message, $user)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject($this->subject)
                    ->view('emails.user_email')
                    ->with([
                        'messages' => $this->message,
                        'userName' => $this->user->name,
                        'userEmail' => $this->user->email,
                        'userPhone' => $this->user->phone_no,
                    ]);
    }
}
