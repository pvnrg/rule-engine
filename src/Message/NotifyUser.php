<?php

namespace App\Message;

class NotifyUser
{
    public function __construct(public readonly int $uploadId, public readonly string $reason)
    {
    }
}
