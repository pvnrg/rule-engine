<?php

namespace App\Service\Contract;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileValidatorInterface
{
    public function validateFile(UploadedFile $file): bool;
}
