<?php

namespace BenjaminFavre\OAuthHttpClient\RequestSigner;

class BearerHeaderRequestSigner extends HeaderRequestSigner
{
    public function __construct()
    {
        parent::__construct('Authorization', 'Bearer {token}');
    }
}