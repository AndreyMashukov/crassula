<?php

namespace App\Service;

use App\Service\Deserializer\AbstractDeserializer;
use InvalidArgumentException;
use LogicException;

class DeserializerProvider
{
    /**
     * @var array
     */
    private array $deserializers = [];

    private SourceConfiguration $configuration;

    public function __construct(\Traversable $deserializers, SourceConfiguration $configuration)
    {
        $this->deserializers = \iterator_to_array($deserializers);
        $this->configuration = $configuration;
    }

    public function getBySource(string $source): AbstractDeserializer
    {
        if (!isset(SourceConfiguration::ALLOWED_SOURCES[$source])) {
            throw new InvalidArgumentException("Source: '{$source}' is not allowed.");
        }

        $deserializer = $this->deserializers[$source] ?? null;

        if (!$deserializer instanceof AbstractDeserializer) {
            throw new LogicException("Deserializer: '{$source}' has not been loaded.");
        }

        return $deserializer;
    }

    public function getDefault(): AbstractDeserializer
    {
        return $this->getBySource($this->configuration->getDefaultSource());
    }
}
