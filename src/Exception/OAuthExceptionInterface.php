<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\Exception;

use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * Represents errors during OAuth protocol.
 */
interface OAuthExceptionInterface extends ExceptionInterface
{
}
