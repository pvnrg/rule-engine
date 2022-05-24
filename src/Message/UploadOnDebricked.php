<?php

namespace App\Message;

class UploadOnDebricked
{
    public function __construct(public readonly int $userId)
    {
    }


}
