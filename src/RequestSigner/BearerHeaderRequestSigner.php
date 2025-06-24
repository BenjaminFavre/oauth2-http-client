<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\RequestSigner;

final class BearerHeaderRequestSigner extends HeaderRequestSigner
{
    public function __construct()
    {
        parent::__construct('Authorization', 'Bearer {token}');
    }
}
