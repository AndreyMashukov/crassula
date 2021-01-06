<?php

namespace App\Component\DTO;

use DateTimeInterface;

class ConverterRequest
{
    private string $currencyFrom;

    private string $currencyTo;

    private int $amount;

    private DateTimeInterface $date;

    public function __construct(string $currencyFrom, string $currencyTo, int $amount, DateTimeInterface $date)
    {
        $this->currencyFrom = $currencyFrom;
        $this->currencyTo   = $currencyTo;
        $this->amount       = $amount;
        $this->date         = $date;
    }

    public function getCurrencyFrom(): string
    {
        return $this->currencyFrom;
    }

    public function getCurrencyTo(): string
    {
        return $this->currencyTo;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }
}
