<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\GrantType;

/**
 * Implementation of one OAuth grant type that returns a refresh token in addition of the access token.
 */
interface RefreshableGrantTypeInterface extends GrantTypeInterface
{
    /**
     * Factory method that builds a refresh grant type corresponding to this grant type.
     *
     * @param string $refreshToken An OAuth refresh token acquired by this grant type.
     *
     * @return GrantTypeInterface A refresh grant type built from this grant type.
     */
    public function getRefreshTokenGrant(string $refreshToken): GrantTypeInterface;
}
