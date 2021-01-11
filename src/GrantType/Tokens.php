<?php

namespace BenjaminFavre\OAuthHttpClient\GrantType;

/**
 * Value object for an access token and a refresh token.
 *
 * @author Benjamin Favre <favre.benjamin@gmail.com>
 */
class Tokens
{
    /** @var string */
    private $accessToken;
    /** @var string|null */
    private $refreshToken;

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