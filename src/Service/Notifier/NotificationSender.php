<?php

namespace App\Service\Notifier;

use App\Entity\Triggere;
use App\Entity\Uploads;

class NotificationSender implements NotificationInterface
{
    /**
     * All services tagged with engine.notifier tag will be injected
     * @param iterable $notifiers
     */
    public function __construct(private readonly iterable $notifiers)
    {
    }

    /**
     * @param Uploads $uploads
     * @param Triggere $triggere
     * @return void
     */
    public function send(Uploads $uploads, Triggere $triggere): void
    {
        /** @var NotifierInterface $notifier */
        foreach ($this->notifiers as $notifier) {

            if (!$notifier->isActivated($triggere->getNotification()->getName())) {
                continue;
            }

            $notifier->send($uploads, $triggere);
        }
    }
}
