<?php

namespace BenjaminFavre\OAuthHttpClient\ResponseChecker;

use Symfony\Contracts\HttpClient\ResponseInterface;

class StatusCode401ResponseChecker implements ResponseCheckerInterface
{
    public function hasAuthenticationFailed(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === 401;
    }
}