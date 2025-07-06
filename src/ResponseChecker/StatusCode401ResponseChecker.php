<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\ResponseChecker;

use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @psalm-api
 */
class StatusCode401ResponseChecker implements ResponseCheckerInterface
{
    #[\Override]
    public function hasAuthenticationFailed(ResponseInterface $response): bool
    {
        return $response->getStatusCode() === 401;
    }
}
