<?php

namespace BenjaminFavre\OAuthHttpClient\GrantType;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implementation of the OAuth client credentials grant type.
 *
 * @author Benjamin Favre <favre.benjamin@gmail.com>
 */
class ClientCredentialsGrantType implements GrantTypeInterface
{
    use TokensExtractor;

    /** @var HttpClientInterface */
    private $client;
    /** @var string */
    private $tokenUrl;
    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;

    /**
     * @param HttpClientInterface $client A HTTP client to be used to communicate with the OAuth server.
     * @param string $tokenUrl The full URL of the token endpoint of the OAuth server.
     * @param string $clientId The OAuth client ID.
     * @param string $clientSecret The OAuth client secret.
     */
    public function __construct(
        HttpClientInterface $client,
        string $tokenUrl,
        string $clientId,
        string $clientSecret
    ) {
        $this->client = $client;
        $this->tokenUrl = $tokenUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * {@inheritDoc}
     *
     * @return Tokens
     * @throws TransportExceptionInterface
     */
    public function getTokens(): Tokens
    {
        $response = $this->client->request('POST', $this->tokenUrl, [
            'headers' => ['Authorization' => sprintf('Basic %s', base64_encode("{$this->clientId}:{$this->clientSecret}"))],
            'body' => http_build_query(['grant_type' => 'client_credentials']),
        ]);

        return $this->extractTokens($response);
    }
}