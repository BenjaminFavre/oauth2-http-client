<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\TokensCache;

use BenjaminFavre\OAuthHttpClient\GrantType\GrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\GrantType\Tokens;
use RuntimeException;
use Symfony\Contracts\Cache\CacheInterface;

class SymfonyTokensCacheAdapter implements TokensCacheInterface
{
    private CacheInterface $cache;

    private string $cacheKey;

    public function __construct(CacheInterface $cache, string $cacheKey)
    {
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
    }

    public function get(GrantTypeInterface $grant): Tokens
    {
        $tokens = $this->cache->get($this->cacheKey, function () use ($grant) {
            return $grant->getTokens();
        });

        if (!$tokens instanceof Tokens) {
            throw new RuntimeException();
        }

        return $tokens;
    }

    public function clear(): void
    {
        $this->cache->delete($this->cacheKey);
    }
}
