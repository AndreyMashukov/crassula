<?php

namespace App\Service\Source;

use App\Service\SourceNameInterface;

interface SourceInterface extends SourceNameInterface
{
    /**
     * Get RAW content.
     *
     * @return string
     */
    public function getRaw(): string;
}
