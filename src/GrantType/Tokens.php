<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\GrantType;

/**
 * Value object for an access token and a refresh token.
 * @psalm-api
 */
class Tokens
{
    private string $accessToken;

    private ?string $refreshToken;

    public function __construct(string $accessToken, ?string $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }
}
