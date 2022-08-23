<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\RequestSigner;

interface RequestSignerInterface
{
    public function modify(array &$options, string $token): void;
}
