<?php

namespace BenjaminFavre\OAuthHttpClient\GrantType;

/**
 * Implementation of one OAuth grant type that returns a refresh token in addition of the access token.
 *
 * @author Benjamin Favre <favre.benjamin@gmail.com>
 */
interface RefreshableGrantTypeInterface extends GrantTypeInterface
{
    /**
     * Factory method that builds a refresh grant type corresponding to this grant type.
     *
     * @param string $refreshToken An OAuth refresh token acquired by this grant type.
     * @return RefreshTokenGrantType A refresh grant type built from this grant type.
     */
    public function getRefreshTokenGrant(string $refreshToken): GrantTypeInterface;
}