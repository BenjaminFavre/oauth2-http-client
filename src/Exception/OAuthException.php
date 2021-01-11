<?php

namespace BenjaminFavre\OAuthHttpClient\Exception;

use RuntimeException;

/**
 * Represents errors during OAuth protocol.
 *
 * @author Benjamin Favre <favre.benjamin@gmail.com>
 */
class OAuthException extends RuntimeException implements OAuthExceptionInterface
{
}