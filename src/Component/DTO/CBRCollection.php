<?php

namespace App\Component\DTO;

use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\XmlRoot("ValCurs")
 */
class CBRCollection extends CurrencyCollection implements DateCollectionInterface
{
    /**
     * @Serializer\XmlElement
     * @Serializer\Type("Doctrine\Common\Collections\ArrayCollection<App\Component\DTO\CBRRate>")
     * @Serializer\XmlList(entry="Valute", inline=true)
     *
     * @var Collection
     */
    protected Collection $collection;

    /**
     * @Serializer\XmlAttribute
     * @Serializer\SerializedName("Date")
     * @Serializer\Type("DateTimeImmutable<'d.m.Y'>")
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
