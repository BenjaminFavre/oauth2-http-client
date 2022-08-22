<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\GrantType;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implementation of the OAuth password grant type.
 */
class PasswordGrantType implements GrantTypeInterface
{
    use TokensExtractor;

    private HttpClientInterface $client;

    private string $tokenUrl;

    private string $username;

    private string $password;

    private ?string $clientId;

    private ?string $clientSecret;

    /**
     * @param HttpClientInterface $client A HTTP client to be used to communicate with the OAuth server.
     * @param string $tokenUrl The full URL of the token endpoint of the OAuth server.
     * @param string $username The OAuth user username.
     * @param string $password The OAuth user password.
     * @param string|null $clientId The OAuth client ID.
     * @param string|null $clientSecret The OAuth client secret.
     */
    public function __construct(
        HttpClientInterface $client,
        string $tokenUrl,
        string $username,
        string $password,
        ?string $clientId = null,
        ?string $clientSecret = null,
    ) {
        $this->client = $client;
        $this->tokenUrl = $tokenUrl;
        $this->username = $username;
        $this->password = $password;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * @inheritDoc
     *
     * @throws TransportExceptionInterface
     */
    public function getTokens(): Tokens
    {
        $parameters = [
            'grant_type' => 'password',
            'username' => $this->username,
            'password' => $this->password,
        ];

        if ($this->clientId !== null && $this->clientSecret !== null) {
            $parameters = array_merge($parameters, [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);
        }

        $response = $this->client->request('POST', $this->tokenUrl, [
            'body' => http_build_query($parameters),
        ]);

        return $this->extractTokens($response);
    }
}
