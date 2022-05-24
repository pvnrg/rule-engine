<?php

namespace App\Message;

class SaveTriggers
{
    public function __construct(public readonly int $userId, public readonly array $inputs)
    {
    }
}
