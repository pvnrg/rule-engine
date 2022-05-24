<?php

namespace App\Service\Contract;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploaderInterface
{
    public function upload(array|UploadedFile $file): Collection;

    public function getUploadDirectory(): string;
}
