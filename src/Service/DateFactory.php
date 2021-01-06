<?php

namespace App\Service;

use DateTimeImmutable;
use DateTimeInterface;

class DateFactory
{
    public function getTodayDate(): DateTimeInterface
    {
        return new DateTimeImmutable('today');
    }
}
