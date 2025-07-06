<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\Exception;

use RuntimeException;

/**
 * Represents errors during OAuth protocol.
 * @psalm-api
 */
class OAuthException extends RuntimeException implements OAuthExceptionInterface
{
}
