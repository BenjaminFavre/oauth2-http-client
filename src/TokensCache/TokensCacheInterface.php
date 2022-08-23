<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\TokensCache;

use BenjaminFavre\OAuthHttpClient\Exception\OAuthException;
use BenjaminFavre\OAuthHttpClient\GrantType\GrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\GrantType\Tokens;

interface TokensCacheInterface
{
    /**
     * @throws OAuthException
     */
    public function get(GrantTypeInterface $grant): Tokens;

    public function clear(): void;
}
