<?php

namespace App\Service\Deserializer;

use App\Component\DTO\CurrencyCollection;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;

abstract class AbstractDeserializer
{
    public const SOURCE_ECB = 'ecb';

    public const SOURCE_CBR = 'cbr';

    public const FORMAT_XML = 'xml';

    public const FORMAT_JSON = 'json';

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function deserialize(string $content): CurrencyCollection
    {
        $result = $this->serializer->deserialize($content, $this->getCollectionType(), $this->getFormat(), $this->getContext());

        return $this->cast($result);
    }

    /**
     * @param CurrencyCollection|object $object
     *
     * @return CurrencyCollection
     */
    protected function cast(object $object): CurrencyCollection
    {
        return $object;
    }

    protected function getContext(): ?DeserializationContext
    {
        return null;
    }

    abstract protected function getFormat(): string;

    abstract protected function getCollectionType(): string;

    abstract public static function getSource(): string;
}
