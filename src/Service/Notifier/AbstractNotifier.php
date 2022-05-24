<?php

namespace App\Service\Notifier;


use App\Entity\Triggere;
use App\Entity\Uploads;
use Symfony\Component\Notifier\NotifierInterface as BaseNotifierInterface;

abstract class AbstractNotifier implements NotifierInterface
{
    public function __construct(protected readonly BaseNotifierInterface $notifier)
    {
    }

    abstract protected function buildContent(Uploads $uploads, Triggere $triggere);
}
