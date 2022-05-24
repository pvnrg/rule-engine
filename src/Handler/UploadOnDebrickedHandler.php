<?php

namespace App\Handler;

use App\Message\NotifyUser;
use App\Message\ScanOnDebricked;
use App\Message\UploadOnDebricked;
use App\Repository\UploadsRepository;
use App\Repository\UserRepository;
use App\Service\Debricked\FileUploader;
use App\Service\Notifier\NotifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

class UploadOnDebrickedHandler implements MessageHandlerInterface
{
    /**
     * @param UploadsRepository $uploadsRepository
     * @param UserRepository $userRepository
     * @param FileUploader $fileUploader
     * @param MessageBusInterface $asyncBus
     */
    public function __construct(
        private readonly UploadsRepository $uploadsRepository,
        private readonly UserRepository $userRepository,
        private readonly FileUploader $fileUploader,
        private readonly MessageBusInterface $asyncBus
    ) {
    }

    /**
     * @param UploadOnDebricked $message
     * @return void
     */
    public function __invoke(UploadOnDebricked $message): void
    {
        $user = $this->userRepository->find($message->userId);

        $uploads = $this->uploadsRepository->fetchToUpload($user);

        if (empty($uploads)) {
            // dispatch scan message
            $this->asyncBus->dispatch(
                new ScanOnDebricked($message->userId),
                [new DispatchAfterCurrentBusStamp()]
            );
            return;
        }

        foreach ($uploads as $upload) {
            try {
                $uploadId = $this->fileUploader->upload($upload);

                $upload->setCiUploadId($uploadId);
                $this->uploadsRepository->add($upload);
            } catch (\Exception) {
                $this->asyncBus->dispatch(
                    new NotifyUser($upload->getId(), NotifierInterface::REASON_UPLOAD_FAIL)
                );
            }
        }

        // dispatch scan message
        $this->asyncBus->dispatch(
            new ScanOnDebricked($message->userId),
            [new DispatchAfterCurrentBusStamp()]
        );
    }
}
