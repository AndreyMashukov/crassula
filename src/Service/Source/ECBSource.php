<?php

namespace App\Service\Source;

use App\Service\SourceConfiguration;

class ECBSource extends AbstractHttpSource
{
    protected function getUri(): string
    {
        return 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSource(): string
    {
        return SourceConfiguration::SOURCE_ECB;
    }
}
