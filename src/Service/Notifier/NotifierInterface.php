<?php

namespace App\Service\Notifier;

use App\Entity\Triggere;
use App\Entity\Uploads;

interface NotifierInterface
{
    const REASON_UPLOAD_FAIL = 'upload_fail';
    const REASON_VULNERABILITY_FOUND = 'vulnerability_found';
    const TYPE_EMAIL = 'email';
    const TYPE_SLACK = 'slack';
    const EMAIL_SUBJECT = 'Rule engine update for your upload';

    public function send(Uploads $uploads, Triggere $triggere): void;

    public function isActivated(string $type): bool;
}
