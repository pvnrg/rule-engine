<?php

namespace App\Service\Notifier;

use App\Entity\Triggere;
use App\Entity\Uploads;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

class EmailNotifier extends AbstractNotifier
{
    /**
     * @param Uploads $uploads
     * @param Triggere $triggere
     * @return void
     */
    public function send(Uploads $uploads, Triggere $triggere): void
    {
        $notification = (new Notification(NotifierInterface::EMAIL_SUBJECT, ['email']))
            ->content($this->buildContent($uploads, $triggere));

        $recipient = new Recipient($uploads->getUser()->getEmail());

        try {
            $this->notifier->send($notification, $recipient);
        } catch (\Exception $exception) {
            // add log
        }
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isActivated(string $type): bool
    {
        return $type === NotifierInterface::TYPE_EMAIL;
    }

    /**
     * @param Uploads $uploads
     * @param Triggere $triggere
     * @return string
     */
    protected function buildContent(Uploads $uploads, Triggere $triggere): string
    {
        return match ($triggere->getRule()->getName()) {
            NotifierInterface::REASON_UPLOAD_FAIL => sprintf('Upload to debricked failed for your file %s', $uploads->getFilename()),
            NotifierInterface::REASON_VULNERABILITY_FOUND => sprintf('Vulnerabilities found for your uploaded file %s', $uploads->getFilename())
        };
    }
}
