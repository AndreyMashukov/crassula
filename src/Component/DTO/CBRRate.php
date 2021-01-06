<?php

namespace App\Component\DTO;

use App\Service\SourceConfiguration;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("Valute")
 */
class CBRRate extends Rate
{
    /**
     * Default: `RUB`.
     *
     * @var string
     */
    protected string $mainCurrency = self::CURRENCY_RUB;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("ID")
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected string $externalId;

    /**
     * @Serializer\XmlElement
     * @Serializer\SerializedName("CharCode")
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected string $secondaryCurrency;

    /**
     * @Serializer\XmlElement
     * @Serializer\SerializedName("Value")
     * @Serializer\Type("float")
     *
     * @var float
     */
    protected float $rate;

    /**
     * @Serializer\XmlElement
     * @Serializer\SerializedName("Name")
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected string $name;

    /**
     * @Serializer\XmlElement
     * @Serializer\SerializedName("Nominal")
     * @Serializer\Type("integer")
     *
     * @var int
     */
    protected int $nominal;

    public static function getSource(): string
    {
        return SourceConfiguration::SOURCE_CBR;
    }

    public function getRate(): float
    {
        return 1 / (parent::getRate() / $this->getNominal());
    }

    public function getFinalRate(): float
    {
        return $this->getRate();
    }
}
