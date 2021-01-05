<?php

namespace App\Service\Deserializer;

use App\Component\DTO\CurrencyCollection;
use App\Component\DTO\ECBEnvelope;

class ECBDeserializer extends AbstractDeserializer
{
    protected function getFormat(): string
    {
        return self::FORMAT_XML;
    }

    protected function getCollectionType(): string
    {
        return ECBEnvelope::class;
    }

    protected function cast(object $object): CurrencyCollection
    {
        if (!$object instanceof ECBEnvelope) {
            throw new \InvalidArgumentException('Expected class: ' . ECBEnvelope::class);
        }

        return $object->getContainer()->getCollection();
    }

    public static function getSource(): string
    {
        return self::SOURCE_ECB;
    }
}
