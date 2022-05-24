<?php

namespace App\Handler;

use App\Entity\Triggere;
use App\Entity\User;
use App\Message\SaveTriggers;
use App\Repository\NotificationRepository;
use App\Repository\RulesRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SaveTriggersHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RulesRepository $rulesRepository,
        private readonly NotificationRepository $notificationRepository
    ) {
    }

    /**
     * @param SaveTriggers $message
     * @return void
     */
    public function __invoke(SaveTriggers $message): void
    {
        /** @var User $user */
        $user = $this->userRepository->find($message->userId);
        $rules = [];
        $notifications = [];
        $triggers = new ArrayCollection();

        // prepare collection of triggers
        foreach ($message->inputs as $input) {
            if (!isset($rules[$input['rule']])) {
                $rules[$input['rule']] = $this->rulesRepository->find($input['rule']);
            }

            foreach ($input['notifications'] as $notificationId) {
                if (!isset($notifications[$notificationId])) {
                    $notifications[$notificationId] = $this->notificationRepository->find($notificationId);
                }

                $trigger = (new Triggere())
                    ->setUser($user)
                    ->setRule($rules[$input['rule']])
                    ->setNotification($notifications[$notificationId]);

                $triggers->add($trigger);
            }
        }

        $user->setTriggeres($triggers);
        $this->userRepository->add($user);
    }
}
