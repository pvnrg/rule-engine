<?php

namespace App\Message;

class ScanOnDebricked
{
    public function __construct(public readonly int $userId)
    {
    }
}
