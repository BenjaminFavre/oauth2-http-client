<?php

namespace BenjaminFavre\OAuthHttpClient\GrantType;

use BenjaminFavre\OAuthHttpClient\Exception\OAuthException;

/**
 * Implementation of one OAuth grant type.
 *
 * @author Benjamin Favre <favre.benjamin@gmail.com>
 */
interface GrantTypeInterface
{
    /**
     * Retrieves tokens from an OAuth server.
     *
     * @return Tokens The tokens retrieved from the OAuth server.
     * @throws OAuthException When the tokens could not be retrieved.
     */
    public function getTokens(): Tokens;
}