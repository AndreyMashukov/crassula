<?php

namespace App\Service;

use App\Component\DTO\Rate;
use BadMethodCallException;

class SourceConfiguration
{
    public const SOURCE_ECB = 'ecb';

    public const SOURCE_CBR = 'cbr';

    public const ALLOWED_SOURCES = [
        self::SOURCE_CBR => true,
        self::SOURCE_ECB => true,
    ];

    public const MAIN_CURRENCIES = [
        self::SOURCE_CBR => Rate::CURRENCY_RUB,
        self::SOURCE_ECB => Rate::CURRENCY_EUR,
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

    public function isMainCurrency(string $currency): bool
    {
        return $this->getMainCurrency() === $currency;
    }

    public function getMainCurrency(): string
    {
        $main = self::MAIN_CURRENCIES[$this->getDefaultSource()] ?? null;

        if (null === $main) {
            throw new BadMethodCallException('Main currency is not detected.');
        }

        return $main;
    }
}
