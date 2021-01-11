<?php

namespace BenjaminFavre\OAuthHttpClient\TokensCache;

use BenjaminFavre\OAuthHttpClient\GrantType\OAuthException;
use BenjaminFavre\OAuthHttpClient\GrantType\GrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\GrantType\Tokens;

interface TokensCacheInterface
{
    /**
     * @param GrantTypeInterface $grant
     * @return Tokens
     * @throws OAuthException
     */
    public function get(GrantTypeInterface $grant): Tokens;

    public function clear(): void;
}