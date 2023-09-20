<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\GrantType;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Implementation of the OAuth client credentials grant type.
 */
class ClientCredentialsGrantType implements GrantTypeInterface
{
    use TokensExtractor;

    private HttpClientInterface $client;

    private string $tokenUrl;

    private string $clientId;

    private string $clientSecret;

    private string $scope;

    /**
     * @param HttpClientInterface $client A HTTP client to be used to communicate with the OAuth server.
     * @param string $tokenUrl The full URL of the token endpoint of the OAuth server.
     * @param string $clientId The OAuth client ID.
     * @param string $clientSecret The OAuth client secret.
     * @param string $scope The scope of the access request.
     */
    public function __construct(
        HttpClientInterface $client,
        string $tokenUrl,
        string $clientId,
        string $clientSecret,
        string $scope = '',
    ) {
        $this->client = $client;
        $this->tokenUrl = $tokenUrl;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = $scope;
    }

    /**
     * @inheritDoc
     *
     * @throws TransportExceptionInterface
     */
    public function getTokens(): Tokens
    {
        $data = ['grant_type' => 'client_credentials'];

        if ($this->scope !== '') {
            $data['scope'] = $this->scope;
        }

        $response = $this->client->request('POST', $this->tokenUrl, [
            'headers' => ['Authorization' => sprintf('Basic %s', base64_encode("{$this->clientId}:{$this->clientSecret}"))],
            'body' => http_build_query($data),
        ]);

        return $this->extractTokens($response);
    }
}
