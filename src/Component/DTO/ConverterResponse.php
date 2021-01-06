<?php

namespace App\Component\DTO;

class ConverterResponse
{
    private ConverterRequest $request;

    private float $amount;

    public function __construct(ConverterRequest $request, float $amount)
    {
        $this->request = $request;
        $this->amount  = $amount;
    }

    /**
     * @return ConverterRequest
     */
    public function getRequest(): ConverterRequest
    {
        return $this->request;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
