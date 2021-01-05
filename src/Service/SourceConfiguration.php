<?php

namespace App\Service;

class SourceConfiguration
{
    public const SOURCE_ECB = 'ecb';

    public const SOURCE_CBR = 'cbr';

    public const ALLOWED_SOURCES = [
        self::SOURCE_CBR => true,
        self::SOURCE_ECB => true,
    ];

    /**
     * @var string
     */
    private string $defaultSource;

    public function __construct(string $defaultSource)
    {
        $this->defaultSource = $defaultSource;
    }

    public function getDefaultSource(): string
    {
        return $this->defaultSource;
    }
}
