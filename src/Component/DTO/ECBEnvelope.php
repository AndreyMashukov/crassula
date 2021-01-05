<?php

namespace App\Component\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("Envelope", prefix="gesmes")
 */
class ECBEnvelope
{
    /**
     * @Serializer\XmlElement
     * @Serializer\SerializedName("Cube")
     * @Serializer\Type("App\Component\DTO\ECBContainer")
     */
    protected ECBContainer $container;

    public function __construct(ECBContainer $container)
    {
        $this->container = $container;
    }

    /**
     * @return ECBContainer
     */
    public function getContainer(): ECBContainer
    {
        return $this->container;
    }
}
