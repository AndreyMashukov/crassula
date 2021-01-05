<?php

namespace App\Service;

use App\Service\Deserializer\AbstractDeserializer;

class DeserializerProvider
{
    /**
     * @var array
     */
    private array $deserializers = [];

    public const ALLOWED_SOURCES = [
        AbstractDeserializer::SOURCE_CBR => true,
        AbstractDeserializer::SOURCE_ECB => true,
    ];

    /**
     * @var string
     */
    private string $defaultSource;

    public function __construct(\Traversable $deserializers, string $defaultSource)
    {
        $this->deserializers = \iterator_to_array($deserializers);
        $this->defaultSource = $defaultSource;
    }

    public function getBySource(string $source): AbstractDeserializer
    {
        if (!isset(self::ALLOWED_SOURCES[$source])) {
            throw new \InvalidArgumentException("Source: '{$source}' is not allowed.");
        }

        $deserializer = $this->deserializers[$source] ?? null;

        if (!$deserializer instanceof AbstractDeserializer) {
            throw new \LogicException("Deserializer: '{$source}' has not been loaded.");
        }

        return $deserializer;
    }

    public function getDefault(): AbstractDeserializer
    {
        return $this->getBySource($this->defaultSource);
    }
}
