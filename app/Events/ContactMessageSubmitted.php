<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContactMessageSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $contactMessageId) {}
}
