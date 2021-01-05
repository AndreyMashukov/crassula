<?php

namespace App\Component\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("Cube")
 */
class ECBContainer
{
    /**
     * @Serializer\XmlElement
     * @Serializer\SerializedName("Cube")
     * @Serializer\Type("App\Component\DTO\ECBCollection")
     *
     * @var ECBCollection
     */
    private ECBCollection $collection;

    public function __construct(ECBCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return ECBCollection
     */
    public function getCollection(): ECBCollection
    {
        return $this->collection;
    }
}
