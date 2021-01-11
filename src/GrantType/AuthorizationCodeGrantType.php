<?php

namespace BenjaminFavre\OAuthHttpClient\GrantType;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implementation of the OAuth authorization grant type.
 *
 * @author Benjamin Favre <favre.benjamin@gmail.com>
 */
class AuthorizationCodeGrantType implements RefreshableGrantTypeInterface
{
    use TokensExtractor;

    /** @var HttpClientInterface */
    private $client;
    /** @var string */
    private $tokenUrl;
    /** @var string */
    private $code;
    /** @var string */
    private $clientId;
    /** @var string */
    private $clientSecret;

    /**
     * @param HttpClientInterface $client A HTTP client to be used to communicate with the OAuth server.
     * @param string $tokenUrl The full URL of the token endpoint of the OAuth server.
     * @param string $code The code received from the OAuth server during the authentication step.
     * @param string $clientId The OAuth client ID.
     * @param string $clientSecret The OAuth client secret.
     */
    public function __construct(
        HttpClientInterface $client,
        string $tokenUrl,
        string $code,
        string $clientId,
        string $clientSecret
    ) {
        $this->client = $client;
        $this->tokenUrl = $tokenUrl;
        $this->code = $code;
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
            'body' => http_build_query([
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $this->code,
            ])
        ]);

        return $this->extractTokens($response);
    }

    public function getRefreshTokenGrant(string $refreshToken): GrantTypeInterface
    {
        return new RefreshTokenGrantType($this->client, $this->tokenUrl, $refreshToken, $this->clientId, $this->clientSecret);
    }
}