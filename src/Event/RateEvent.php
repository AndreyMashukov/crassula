<?php

namespace App\Event;

use App\Component\DTO\Rate;
use App\Entity\Rate as Entity;
use Symfony\Contracts\EventDispatcher\Event;

class RateEvent extends Event
{
    private Rate $rate;

    private ?Entity $entity = null;

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

    /**
     * @return null|Entity
     */
    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    /**
     * @param null|Entity $entity
     */
    public function setEntity(?Entity $entity): void
    {
        $this->entity = $entity;
    }
}
