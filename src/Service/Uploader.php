<?php

namespace App\Service;

use App\DTO\Uploads;
use App\DTO\UploadStatus;
use App\Service\Contract\FileValidatorInterface;
use App\Service\Contract\UploaderInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class Uploader implements UploaderInterface
{
    /**
     * @param string $uploadDir
     * @param SluggerInterface $slugger
     * @param FileValidatorInterface $validator
     */
    public function __construct(
        private readonly string $uploadDir,
        private readonly SluggerInterface $slugger,
        private readonly FileValidatorInterface $validator,
        private readonly Security $security
    ) {
    }

    /**
     * @param array|UploadedFile $file
     * @return Collection
     */
    public function upload(array|UploadedFile $file): Collection
    {
        $result = new Uploads();
        if (!is_array($file)) {
            $result->add($this->handleUpload($file));

            return $result;
        }

        foreach ($file as $uploadedFile) {
            if (!$uploadedFile instanceof UploadedFile) {
                // log | exception?
                continue;
            }

            $result->add($this->handleUpload($uploadedFile));
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getUploadDirectory(): string
    {
        $user = $this->security->getUser()->getId();
        return sprintf('%s/%s', $this->uploadDir, $user);
    }

    /**
     * Uploads each file to storage.
     * @param UploadedFile $file
     * @return UploadStatus
     */
    private function handleUpload(UploadedFile $file): UploadStatus
    {
        try {
            if (!$this->validator->validateFile($file)) {
                throw new \InvalidArgumentException('Uploaded file seems invalid.');
            }
            $uploadName = $this->moveFile($file);

            return new UploadStatus(
                $this->getFileOriginalName($file),
                UploadStatus::STATUS_SUCCESS,
                $uploadName
            );
        } catch (\Exception $exception) {
            // log or trigger notification
            return new UploadStatus(
                fileOriginalName: $this->getFileOriginalName($file),
                status: UploadStatus::STATUS_FAILED,
                error: $exception->getMessage() // todo: put a better message here for end user and add this error to log
            );
        }
        return new UploadStatus(
            fileOriginalName: $this->getFileOriginalName($file),
            status: UploadStatus::STATUS_FAILED
        );
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function moveFile(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        // .lock will be guessed as text file, so keep .lock extension
        $ext = ($file->getClientOriginalExtension() === 'lock') ? $file->getClientOriginalExtension() : $file->guessExtension();
        $fileName = sprintf('%s_%s.%s', $safeFilename, uniqid(), $ext);

        if (!is_dir($this->getUploadDirectory())) {
            mkdir($this->getUploadDirectory());
        }

        $file->move($this->getUploadDirectory(), $fileName);

        return $fileName;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function getFileOriginalName(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        return sprintf('%s.%s', $originalFilename, $file->getClientOriginalExtension());
    }
}
