<?php

namespace App\Service\Source;

use App\Service\SourceConfiguration;

class CBRSource extends AbstractHttpSource
{
    protected function getUri(): string
    {
        return 'https://www.cbr.ru/scripts/XML_daily.asp';
    }

    /**
     * {@inheritdoc}
     */
    public static function getSource(): string
    {
        return SourceConfiguration::SOURCE_CBR;
    }
}
