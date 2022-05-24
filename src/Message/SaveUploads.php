<?php

namespace App\Message;

use App\DTO\Uploads;

class SaveUploads
{
    public function __construct(
        public readonly int $user,
        public readonly Uploads $uploads
    ) {
    }
}
