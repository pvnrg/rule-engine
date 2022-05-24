<?php

namespace App\Handler;

use App\Message\SaveUploads;
use App\Message\UploadOnDebricked;
use App\Repository\UploadsRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SaveUploadsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UploadsRepository $uploadsRepository,
        private readonly MessageBusInterface $asyncBus
    ) {
    }

    public function __invoke(SaveUploads $message)
    {
        try {
            $user = $this->userRepository->find($message->user);
            $this->uploadsRepository->addUserUploads($message->uploads, $user);

            $this->asyncBus->dispatch(new UploadOnDebricked($user->getId()));
        } catch (\Exception) {
            // try again?
        }
    }
}
