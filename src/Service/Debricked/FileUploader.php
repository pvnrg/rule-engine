<?php

namespace App\Service\Debricked;

use App\Entity\Uploads;

class FileUploader
{
    private Token $token;

    /**
     * @param DebrickedApiManager $apiManager
     * @param string $uploadRoot
     */
    public function __construct(private readonly DebrickedApiManager $apiManager, private readonly string $uploadRoot)
    {
        $this->authenticate();
    }

    /**
     * @param Uploads $uploads
     * @return string
     */
    public function upload(Uploads $uploads): string
    {
        $headers = [
            "accept: */*",
            "Authorization: $this->token",
            "Content-Type: multipart/form-data"
        ];

        $data = [
            'commitName' => 'api',
            'repositoryName' => 'rule-engine',
            'fileData' => new \CURLFile($this->getFilePath($uploads))
        ];

        $response = $this->apiManager->upload($data, $headers);

        if (isset($response['ciUploadId'])) {
            return $response['ciUploadId'];
        }

        // todo: add log with actual response
        throw new \RuntimeException('Invalid response from debricked.');
    }

    /**
     * @return void
     */
    private function authenticate(): void
    {
        $this->token = $this->apiManager->authenticate();
    }

    /**
     * @param Uploads $uploads
     * @return string
     */
    private function getFilePath(Uploads $uploads) : string
    {
        return sprintf('%s/%s/%s', $this->uploadRoot, $uploads->getUser()->getId(), $uploads->getFilename());
    }
}
