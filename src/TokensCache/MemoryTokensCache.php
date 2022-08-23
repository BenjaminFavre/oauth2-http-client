<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\TokensCache;

use BenjaminFavre\OAuthHttpClient\GrantType\GrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\GrantType\Tokens;

class MemoryTokensCache implements TokensCacheInterface
{
    private ?Tokens $tokens = null;

    public function get(GrantTypeInterface $grant): Tokens
    {
        if ($this->tokens === null) {
            $this->tokens = $grant->getTokens();
        }

        return $this->tokens;
    }

    public function clear(): void
    {
        $this->tokens = null;
    }
}
