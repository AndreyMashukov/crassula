<?php

namespace App\Event;

use App\Component\DTO\Rate;
use Symfony\Contracts\EventDispatcher\Event;

class RateEvent extends Event
{
    private Rate $rate;

    public function __construct(Rate $rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return Rate
     */
    public function getRate(): Rate
    {
        return $this->rate;
    }
}
