<?php

namespace App\Controller;

use App\Message\SaveUploads;
use App\Message\ScanOnDebricked;
use App\Service\Contract\UploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UploadController extends AbstractController
{

    #[Route('/upload', name: 'upload_files', methods: 'POST')]
    public function index(Request $request, UploaderInterface $uploader, MessageBusInterface $asyncBus, Security $security): Response
    {
        $files = $request->files->get('dependency');

        if (empty($files)) {
            return $this->json([
                'message' => 'No files uploaded.'
            ]);
        }

        try {
            $asyncBus->dispatch(new ScanOnDebricked($security->getUser()->getId()));
            $result = $uploader->upload($files);

            // files uploaded, now save in database
            $asyncBus->dispatch(new SaveUploads($security->getUser()->getId(), $result));

            return $this->json([
                'message' => 'Upload complete. You will be notified once scan is complete.',
                'data' => $result
            ]);
        } catch (\Exception $exception) {
            // add log
        }

        return $this->json([
            'message' => 'Something went wrong.'
        ]);
    }
}
