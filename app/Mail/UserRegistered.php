<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserRegistered extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationCode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, $verificationCode)
    {
        $this->user = $user;
        $this->verificationCode = $verificationCode;
    }

    /** 
     * Build the message.
     * 
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bienvenido a Fox Hound')
                    ->view('emails.user_registered')
                    ->with([
                        'user', $this->user,
                        'verificationCode', $this->verificationCode
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
