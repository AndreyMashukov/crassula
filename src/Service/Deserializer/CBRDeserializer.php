<?php

namespace App\Service\Deserializer;

use App\Component\DTO\CBRCollection;

class CBRDeserializer extends AbstractDeserializer
{
    protected function getFormat(): string
    {
        return self::FORMAT_XML;
    }

    protected function getCollectionType(): string
    {
        return CBRCollection::class;
    }

    public static function getSource(): string
    {
        return self::SOURCE_CBR;
    }
}
