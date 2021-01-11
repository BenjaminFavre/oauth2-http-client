<?php

namespace BenjaminFavre\OAuthHttpClient\RequestSigner;

interface RequestSignerInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function modify(array &$options, string $token): void;
}