<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\GrantType;

use BenjaminFavre\OAuthHttpClient\Exception\OAuthException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Convenient trait to extract tokens from oauth server response.
 */
trait TokensExtractor
{
    /**
     * Extracts access token and refresh token from the JSON response of the OAuth server.
     *
     * @param ResponseInterface $response The response from the token endpoint of the OAuth server.
     *
     * @return Tokens The access token and the refresh token extracted from the response.
     */
    private function extractTokens(ResponseInterface $response): Tokens
    {
        try {
            $responseBody = $response->getContent();
        } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
            throw new OAuthException('Error when calling token endpoint.', 0, $e);
        }

        $token = json_decode($responseBody, true);

        if ($token === null && $responseBody !== 'null') {
            throw new OAuthException('Error when parsing token endpoint JSON response.');
        }

        if (!is_array($token) || !array_key_exists('access_token', $token) || !is_string($token['access_token'])) {
            throw new OAuthException('Access token not found in token endpoint response.');
        }

        return new Tokens($token['access_token'], $token['refresh_token'] ?? null);
    }
}
