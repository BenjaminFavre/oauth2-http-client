<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\TokensCache;

use BenjaminFavre\OAuthHttpClient\GrantType\GrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\GrantType\Tokens;

/**
 * @psalm-api
 */
class MemoryTokensCache implements TokensCacheInterface
{
    private ?Tokens $tokens = null;

    #[\Override]
    public function get(GrantTypeInterface $grant): Tokens
    {
        if ($this->tokens === null) {
            $this->tokens = $grant->getTokens();
        }

        return $this->tokens;
    }

    #[\Override]
    public function clear(): void
    {
        $this->tokens = null;
    }
}
