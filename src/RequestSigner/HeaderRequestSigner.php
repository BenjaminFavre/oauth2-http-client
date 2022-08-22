<?php

declare(strict_types=1);

namespace BenjaminFavre\OAuthHttpClient\RequestSigner;

class HeaderRequestSigner implements RequestSignerInterface
{
    private string $headerName;

    private string $headerValueFormat;

    public function __construct(string $headerName, string $headerValueFormat)
    {
        $this->headerName = $headerName;
        $this->headerValueFormat = $headerValueFormat;
    }

    public function modify(array &$options, string $token): void
    {
        $options['headers'][$this->headerName] = str_replace('{token}', $token, $this->headerValueFormat);
    }
}
