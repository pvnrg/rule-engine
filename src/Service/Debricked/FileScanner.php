<?php

namespace App\Service\Debricked;

use App\Entity\Uploads;

class FileScanner
{
    private Token $token;

    /**
     * @param DebrickedApiManager $apiManager
     */
    public function __construct(private readonly DebrickedApiManager $apiManager)
    {
        $this->authenticate();
    }

    public function scan(Uploads $uploads): array
    {
        // first we add upload to scan queue
        $this->moveToScanQueue($uploads);

        // now we get scan results
        $headers = [
            "accept: */*",
            "Authorization: $this->token"
        ];

        $data = [
            "ciUploadId" => $uploads->getCiUploadId()
        ];

        return $this->apiManager->scan($data, $headers);
    }

    private function moveToScanQueue(Uploads $uploads)
    {
        $headers = [
            "accept: */*",
            "Authorization: $this->token",
            "Content-Type: application/json"
        ];

        $data = [
            "ciUploadId" => $uploads->getCiUploadId(),
            "repositoryName" => null,
            "integrationName" => null,
            "commitName" => null,
            "author" => null
        ];

        $responseCode = $this->apiManager->finishUpload($data, $headers);

        if ($responseCode !== 204) {
            throw new \Exception('Failed to move file to scan queue on debricked.');
        }
    }

    /**
     * @return void
     */
    private function authenticate(): void
    {
        $this->token = $this->apiManager->authenticate();
    }
}
