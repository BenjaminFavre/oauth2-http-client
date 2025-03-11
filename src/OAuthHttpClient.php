<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient;

use BenjaminFavre\OAuthHttpClient\GrantType\GrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\GrantType\RefreshableGrantTypeInterface;
use BenjaminFavre\OAuthHttpClient\RequestSigner\BearerHeaderRequestSigner;
use BenjaminFavre\OAuthHttpClient\RequestSigner\RequestSignerInterface;
use BenjaminFavre\OAuthHttpClient\ResponseChecker\ResponseCheckerInterface;
use BenjaminFavre\OAuthHttpClient\ResponseChecker\StatusCode401ResponseChecker;
use BenjaminFavre\OAuthHttpClient\TokensCache\MemoryTokensCache;
use BenjaminFavre\OAuthHttpClient\TokensCache\TokensCacheInterface;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class OAuthHttpClient implements HttpClientInterface
{
    private HttpClientInterface $client;

    private GrantTypeInterface $grant;

    private RequestSignerInterface $signer;

    private ResponseCheckerInterface $checker;

    private TokensCacheInterface $cache;

    public function __construct(
        HttpClientInterface $client,
        GrantTypeInterface $grant,
    ) {
        $this->client = $client;
        $this->grant = $grant;
        $this->signer = new BearerHeaderRequestSigner();
        $this->checker = new StatusCode401ResponseChecker();
        $this->cache = new MemoryTokensCache();
    }

    public function setSigner(RequestSignerInterface $signer): self
    {
        $this->signer = $signer;

        return $this;
    }

    public function setChecker(ResponseCheckerInterface $checker): self
    {
        $this->checker = $checker;

        return $this;
    }

    public function setCache(TokensCacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $grant = $this->grant;

        for ($tries = 0; $tries < 2; ++$tries) {
            $tokens = $this->cache->get($grant);
            $this->signer->modify($options, $tokens->getAccessToken());
            $response = $this->client->request($method, $url, $options);

            if (!$this->checker->hasAuthenticationFailed($response)) {
                return $response;
            }

            $this->cache->clear();

            if ($grant instanceof RefreshableGrantTypeInterface && ($refreshToken = $tokens->getRefreshToken()) !== null) {
                $grant = $grant->getRefreshTokenGrant($refreshToken);
            }
        }

        throw new RuntimeException();
    }

    public function stream($responses, ?float $timeout = null): ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }

    public function withOptions(array $options): static
    {
        return new self($this->client->withOptions($options), $this->grant);
    }
}
