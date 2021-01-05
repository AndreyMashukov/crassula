<?php

namespace App\Component\DTO;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CurrencyCollection
{
    protected Collection $collection;

    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }

    public function addRate(Rate $rate): self
    {
        $this->collection->add($rate);

        return $this;
    }

    public function getRates(): Collection
    {
        return $this->collection;
    }

    public function removeRate(Rate $rate): self
    {
        $this->collection->removeElement($rate);

        return $this;
    }
}
