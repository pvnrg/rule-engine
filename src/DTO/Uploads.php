<?php

namespace App\DTO;

use Doctrine\Common\Collections\ArrayCollection;

class Uploads extends ArrayCollection implements \JsonSerializable
{
    public function __construct(array $elements = [])
    {
        foreach ($elements as $element) {
            if (!$element instanceof UploadStatus) {
                throw new \InvalidArgumentException('Each element must be instance of UploadStatus class.');
            }
        }
        parent::__construct($elements);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
