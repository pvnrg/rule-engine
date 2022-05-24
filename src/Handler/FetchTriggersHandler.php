<?php

namespace App\Handler;

use App\Entity\Triggere;
use App\Message\FetchTriggers;
use App\Repository\NotificationRepository;
use App\Repository\RulesRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FetchTriggersHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RulesRepository $rulesRepository,
        private readonly NotificationRepository $notificationRepository
    ) {
    }

    public function __invoke(FetchTriggers $message): array
    {
        $rules = $this->rulesRepository->findAll();
        $notifications = $this->notificationRepository->findAll();
        $user = $this->userRepository->find($message->userId);

        return [
            'rules' => array_map(function($rule) { return ['id' => $rule->getId(), 'name' => $rule->getName()]; }, $rules),
            'notifications' => array_map(function($notification) { return ['id' => $notification->getId(), 'name' => $notification->getName()]; }, $notifications),
            'triggers' => $user->getTriggeres()->map(function (Triggere $triggere) {
                return [
                    'rule' => $triggere->getRule()->getName(),
                    'notification' => $triggere->getNotification()->getName()
                ];
            })->toArray()
        ];
    }
}
