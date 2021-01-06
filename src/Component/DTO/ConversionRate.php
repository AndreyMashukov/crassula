<?php

namespace App\Component\DTO;

class ConversionRate
{
    private float $rate;

    private bool $reversed;

    public function __construct(float $rate, bool $reversed)
    {
        $this->rate     = $rate;
        $this->reversed = $reversed;
    }

    public function getRate(): float
    {
        if ($this->reversed) {
            return 1 / $this->rate;
        }

        return $this->rate;
    }
}
