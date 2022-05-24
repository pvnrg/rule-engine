<?php

namespace App\Service;

class CurlGet
{
    public function __construct(private readonly string $url, private readonly array $options = [])
    {
    }

    public function __invoke(): array
    {
        $ch = \curl_init($this->url);

        foreach ($this->options as $key => $val) {
            \curl_setopt($ch, $key, $val);
        }

        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);

        $response = \curl_exec($ch);
        $error    = \curl_error($ch);
        $errno    = \curl_errno($ch);
        $responseCode = \curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if (\is_resource($ch)) {
            \curl_close($ch);
        }

        if (0 !== $errno) {
            throw new \RuntimeException($error, $errno);
        }

        return [
            'responseCode' => $responseCode,
            'data' => json_decode($response, true)
        ];
    }
}
