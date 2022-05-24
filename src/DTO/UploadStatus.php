<?php

namespace App\DTO;

class UploadStatus implements \JsonSerializable
{
    const STATUS_PENDING = 'pending';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    /**
     * @param string $fileOriginalName
     * @param string $status
     * @param string|null $fileUploadName
     */
    public function __construct(
        public readonly string $fileOriginalName,
        public readonly string $status,
        public readonly ?string $fileUploadName = null
    ) {
        if ($this->status === self::STATUS_SUCCESS && null === $this->fileUploadName) {
            throw new \InvalidArgumentException('Something is wrong here. Status and upload name does not match.');
        }
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'file' => $this->fileOriginalName,
            'status' => $this->status
        ];
    }
}
