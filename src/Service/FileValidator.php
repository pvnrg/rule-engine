<?php

namespace App\Service;

use App\Service\Contract\FileValidatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FileValidator implements FileValidatorInterface
{
    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    /**
     * @param UploadedFile $file
     * @return bool
     */
    public function validateFile(UploadedFile $file): bool
    {
        $constraint = new Assert\File([
            'mimeTypes' => [
                'text/plain',
                'application/json'
            ],
            'maxSize' => '2M'
        ]);

        $errors = $this->validator->validate($file, $constraint);

        return $errors->count() === 0;
    }
}
