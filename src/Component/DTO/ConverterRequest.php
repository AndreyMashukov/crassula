<?php

namespace App\Component\DTO;

use DateTimeInterface;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Serializer\ExclusionPolicy(Serializer\ExclusionPolicy::ALL)
 */
class ConverterRequest
{
    /**
     * @Serializer\Expose
     *
     * @var string
     *
     * @Assert\NotBlank
     */
    private string $currencyFrom;

    /**
     * @Serializer\Expose
     *
     * @var string
     *
     * @Assert\NotBlank
     */
    private string $currencyTo;

    /**
     * @Serializer\Expose
     *
     * @var int
     */
    private int $amount;

    /**
     * @Serializer\Expose
     *
     * @var DateTimeInterface
     */
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
