<?php

namespace App\Component\DTO;

use App\Service\SourceConfiguration;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("Cube")
 */
class ECBRate extends Rate
{
    /**
     * Default: `EUR`.
     *
     * @var string
     */
    protected string $mainCurrency = self::CURRENCY_EUR;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("currency")
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected string $externalId; // no EID in xml, use currency instead.

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("currency")
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected string $secondaryCurrency;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("rate")
     * @Serializer\Type("float")
     *
     * @var float
     */
    protected float $rate;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("currency")
     * @Serializer\Type("string")
     *
     * @var string
     */
    protected string $name; // no name in xml, use currency instead.

    /**
     * Default `1`.
     *
     * @var int
     */
    protected int $nominal = 1;

    public static function getSource(): string
    {
        return SourceConfiguration::SOURCE_ECB;
    }
}
