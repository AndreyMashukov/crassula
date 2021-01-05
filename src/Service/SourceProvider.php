<?php

namespace App\Service;

use App\Service\Source\SourceInterface;
use InvalidArgumentException;
use LogicException;

class SourceProvider
{
    /**
     * @var array
     */
    private array $sources = [];

    public function __construct(\Traversable $sources)
    {
        $this->sources = \iterator_to_array($sources);
    }

    public function getBySource(string $source): SourceInterface
    {
        if (!isset(SourceConfiguration::ALLOWED_SOURCES[$source])) {
            throw new InvalidArgumentException("Source: '{$source}' is not allowed.");
        }

        $source = $this->sources[$source] ?? null;

        if (!$source instanceof SourceInterface) {
            throw new LogicException("Source: '{$source}' has not been loaded.");
        }

        return $source;
    }
}
