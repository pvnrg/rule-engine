<?php

namespace App\Handler;

use App\Entity\Uploads;
use App\Message\NotifyUser;
use App\Message\ScanOnDebricked;
use App\Repository\UploadsRepository;
use App\Repository\UserRepository;
use App\Service\Debricked\FileScanner;
use App\Service\Notifier\NotifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class ScanOnDebrickedHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UploadsRepository $uploadsRepository,
        private readonly UserRepository $userRepository,
        private readonly FileScanner $fileScanner,
        private readonly MessageBusInterface $asyncBus
    ) {
    }

    public function __invoke(ScanOnDebricked $message)
    {
        $user = $this->userRepository->find($message->userId);

        // fetch list of files which are not scanned yet
        $uploads = $this->uploadsRepository->fetchToScan($user);

        if (empty($uploads)) {
            return;
        }

        /** @var Uploads $upload */
        foreach ($uploads as $upload) {
            try {
                $result = $this->fileScanner->scan($upload);

                if (isset($result['vulnerabilitiesFound']) && $result['vulnerabilitiesFound'] > 0) {
                    $this->asyncBus->dispatch(
                        new NotifyUser($upload->getId(), NotifierInterface::REASON_VULNERABILITY_FOUND)
                    );
                }
                $upload->setIsScanned(true)
                    ->setScanResult(json_encode($result));
            } catch (\Exception $exception) {
                $this->asyncBus->dispatch(
                    new NotifyUser($upload->getId(), NotifierInterface::REASON_UPLOAD_FAIL)
                );
            }
        }
    }
}
