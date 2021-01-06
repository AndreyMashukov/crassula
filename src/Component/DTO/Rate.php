<?php

namespace App\Component\DTO;

use App\Service\SourceNameInterface;

abstract class Rate implements SourceNameInterface
{
    public const CURRENCY_RUB = 'RUB';

    public const CURRENCY_EUR = 'EUR';

    /**
     * @var string
     */
    protected string $externalId;

    /**
     * @var string
     */
    protected string $mainCurrency;

    /**
     * @var string
     */
    protected string $secondaryCurrency;

    /**
     * @var float
     */
    protected float $rate;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var int
     */
    protected int $nominal;

    /**
     * @var null|\DateTimeInterface
     */
    private ?\DateTimeInterface $date = null;

    public function __construct(string $name, string $externalId, string $mainCurrency, string $secondaryCurrency, float $rate, int $nominal = 1)
    {
        $this->name              = $name;
        $this->externalId        = $externalId;
        $this->mainCurrency      = $mainCurrency;
        $this->secondaryCurrency = $secondaryCurrency;
        $this->rate              = $rate;
        $this->nominal           = $nominal;
    }

    /**
     * @return string
     */
    public function getMainCurrency(): string
    {
        return $this->mainCurrency;
    }

    /**
     * @return string
     */
    public function getSecondaryCurrency(): string
    {
        return $this->secondaryCurrency;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getNominal(): int
    {
        return $this->nominal;
    }

    /**
     * @return null|\DateTimeInterface
     */
    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param null|\DateTimeInterface $date
     *
     * @return Rate
     */
    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFinalRate(): float
    {
        return $this->getRate() / $this->getNominal();
    }

    abstract public static function getSource(): string;
}
