<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\GrantType;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implementation of the OAuth authorization grant type.
 */
final class AuthorizationCodeGrantType implements RefreshableGrantTypeInterface
{
    use TokensExtractor;

    private HttpClientInterface $client;

    private string $tokenUrl;

    private string $code;

    private string $clientId;

    private string $clientSecret;

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
        string $clientSecret,
    ) {
        $this->client = $client;
        $this->tokenUrl = $tokenUrl;
        $this->code = $code;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @inheritDoc
     *
     * @throws TransportExceptionInterface
     */
    #[\Override]
    public function getTokens(): Tokens
    {
        $response = $this->client->request('POST', $this->tokenUrl, [
            'body' => http_build_query([
                'grant_type' => 'authorization_code',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $this->code,
            ]),
        ]);

        return $this->extractTokens($response);
    }

    #[\Override]
    public function getRefreshTokenGrant(string $refreshToken): GrantTypeInterface
    {
        return new RefreshTokenGrantType($this->client, $this->tokenUrl, $refreshToken, $this->clientId, $this->clientSecret);
    }
}
