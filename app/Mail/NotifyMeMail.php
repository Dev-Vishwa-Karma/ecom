<?php
namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyMeMail extends Mailable
{
    use SerializesModels;

    public $variant;
    public $sellerName;
    public $sellerEmail;

public function __construct($variant, $sellerName, $sellerEmail)
{
    $this->variant = $variant;
    $this->sellerName = $sellerName;
    $this->sellerEmail = $sellerEmail;
}

 public function build()
{
    return $this->from(config('mail.from.address'), config('mail.from.name'))
                ->replyTo($this->sellerEmail, $this->sellerName)
                ->subject('Product Back in Stock')
                ->view('emails.notify_me');
}
}