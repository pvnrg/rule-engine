<?php

namespace App\Handler;

use App\Message\NotifyUser;
use App\Repository\UploadsRepository;
use App\Service\Notifier\NotificationInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotifyUserHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UploadsRepository $uploadsRepository,
        private readonly NotificationInterface $notifier
    ) {
    }

    public function __invoke(NotifyUser $message)
    {
        $upload = $this->uploadsRepository->find($message->uploadId);

        // user has not enabled the rule to notify
        if (!$upload || !$upload->getUser()->isTriggerOn($message->reason)) {
            return;
        }
        // fetch all triggers for current rule
        $triggers = $upload->getUser()->getRuleTriggers($message->reason);

        foreach ($triggers as $trigger) {
            $this->notifier->send($upload, $trigger);
        }
    }
}
