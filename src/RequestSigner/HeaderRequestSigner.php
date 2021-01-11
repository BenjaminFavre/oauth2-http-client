<?php

namespace BenjaminFavre\OAuthHttpClient\RequestSigner;

class HeaderRequestSigner implements RequestSignerInterface
{
    /** @var string */
    private $headerName;
    /** @var string */
    private $headerValueFormat;

    public function __construct(string $headerName, string  $headerValueFormat)
    {
        $this->headerName = $headerName;
        $this->headerValueFormat = $headerValueFormat;
    }

    public function modify(array &$options, string $token): void
    {
        $options['headers'][$this->headerName] = str_replace('{token}', $token, $this->headerValueFormat);
    }
}