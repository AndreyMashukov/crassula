<?php

namespace App\Component\DTO;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy(Serializer\ExclusionPolicy::ALL)
 */
class ConverterResponse
{
    /**
     * @Serializer\Expose
     *
     * @var ConverterRequest
     */
    private ConverterRequest $request;

    /**
     * @Serializer\Expose
     *
     * @var float
     */
    private float $result;

    public function __construct(ConverterRequest $request, float $result)
    {
        $this->request = $request;
        $this->result  = $result;
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
    public function getResult(): float
    {
        return $this->result;
    }
}
