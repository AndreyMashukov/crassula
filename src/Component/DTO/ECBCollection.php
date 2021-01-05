<?php

namespace App\Component\DTO;

use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("Cube")
 */
class ECBCollection extends CurrencyCollection implements DateCollectionInterface
{
    /**
     * @Serializer\XmlElement
     * @Serializer\Type("Doctrine\Common\Collections\ArrayCollection<App\Component\DTO\ECBRate>")
     * @Serializer\XmlList(entry="Cube", inline=true)
     *
     * @var Collection
     */
    protected Collection $collection;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("time")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     *
     * @var \DateTimeInterface
     */
    private \DateTimeInterface $date;

    public function __construct(\DateTimeInterface $date)
    {
        parent::__construct();

        $this->date = $date;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }
}
