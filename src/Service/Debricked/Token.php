<?php

namespace App\Service\Debricked;

class Token implements \Stringable
{
    public function __construct(private readonly string $token, private readonly string $refreshToken)
    {
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function __toString(): string
    {
        return sprintf('Bearer %s', $this->getToken());
    }
}
