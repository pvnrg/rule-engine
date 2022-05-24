<?php

namespace App\Service\Debricked;

use App\Service\CurlGet;
use App\Service\CurlPost;

class DebrickedApiManager
{
    const DEBRICKED_BASE_ROUTE = 'https://debricked.com';
    const DEBRICKED_AUTH_ENDPOINT = 'api/login_check';
    const DEBRICKED_UPLOAD_ENDPOINT = 'api/%s/open/uploads/dependencies/files';
    const DEBRICKED_UPLOAD_FINISH_ENDPOINT = 'api/%s/open/finishes/dependencies/files/uploads';
    const DEBRICKED_SCAN_ENDPOINT = 'api/%s/open/ci/upload/status';
    private ?Token $token = null;

    /**
     * @param array $config
     */
    public function __construct(private readonly array $config)
    {
    }

    /**
     * @return Token
     */
    public function authenticate(): Token
    {
        $curlPost = new CurlPost($this->getAuthEndpoint());
        $result = $curlPost($this->getAuthPayload());
        $result = $result['data'];

        return new Token($result['token'], $result['refresh_token']);
    }

    /**
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function upload(array $data, array $headers): array
    {
        $curlPost = new CurlPost($this->getUploadEndpoint(), [CURLOPT_HTTPHEADER => $headers]);
        $result = $curlPost($data);
        return $result['data'];
    }

    public function finishUpload(array $data, array $headers): int
    {
        // first we ask debricked to move uploaded resource to scan queue
        $curlPost = new CurlPost($this->getUploadFinishEndpoint(), [CURLOPT_HTTPHEADER => $headers]);
        $result = $curlPost($data, true);

        return $result['responseCode'];
    }

    public function scan(array $data, array $headers): array
    {
        // scan status is get request
        $curlGet = new CurlGet($this->getScanEndpoint($data['ciUploadId']), [CURLOPT_HTTPHEADER => $headers]);
        $result = $curlGet();

        return $result['data'];
    }

    /**
     * @return string
     */
    private function getAuthEndpoint(): string
    {
        return sprintf('%s/%s', self::DEBRICKED_BASE_ROUTE, self::DEBRICKED_AUTH_ENDPOINT);
    }

    /**
     * @return string
     */
    private function getUploadEndpoint(): string
    {
        $uploadEndpoint = sprintf(self::DEBRICKED_UPLOAD_ENDPOINT, $this->config['version']);
        return sprintf('%s/%s', self::DEBRICKED_BASE_ROUTE, $uploadEndpoint);
    }

    /**
     * @return string
     */
    private function getScanEndpoint(string $uploadId): string
    {
        $scanEndpoint = sprintf(self::DEBRICKED_SCAN_ENDPOINT, $this->config['version']);
        return sprintf('%s/%s?ciUploadId=%s', self::DEBRICKED_BASE_ROUTE, $scanEndpoint, $uploadId);
    }

    /**
     * @return string
     */
    private function getUploadFinishEndpoint(): string
    {
        $endpoint = sprintf(self::DEBRICKED_UPLOAD_FINISH_ENDPOINT, $this->config['version']);
        return sprintf('%s/%s', self::DEBRICKED_BASE_ROUTE, $endpoint);
    }

    /**
     * @return array
     */
    private function getAuthPayload(): array
    {
        return [
            '_username' => $this->config['username'],
            '_password' => $this->config['password'],
        ];
    }
}
