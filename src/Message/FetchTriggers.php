<?php

namespace App\Message;

class FetchTriggers
{
    public function __construct(public readonly int $userId)
    {
    }
}
