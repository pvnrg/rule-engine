<?php

namespace App\Service\Notifier;

use App\Entity\Triggere;
use App\Entity\Uploads;

interface NotificationInterface
{
    public function send(Uploads $uploads, Triggere $triggere): void;
}
