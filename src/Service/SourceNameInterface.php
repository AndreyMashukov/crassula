<?php

namespace App\Service;

interface SourceNameInterface
{
    /**
     * Get source name.
     *
     * @return string
     */
    public static function getSource(): string;
}
