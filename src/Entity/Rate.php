<?php

namespace App\Entity;

use App\Repository\RateRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="rate", indexes={
 *     @ORM\Index(columns={"rte_date", "rte_source", "rte_secondary", "rte_main"})
 * })
 * @ORM\Entity(repositoryClass=RateRepository::class)
 *
 * @UniqueEntity(fields={"date", "source", "secondaryCurrency", "mainCurrency"})
 *
 * @Serializer\ExclusionPolicy(Serializer\ExclusionPolicy::ALL)
 */
class Rate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="rte_id", type="integer")
     *
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(name="rte_source", type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     *
     * @Serializer\Expose
     */
    private $source;

    /**
     * @ORM\Column(name="rte_name", type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     *
     * @Serializer\Expose
     */
    private $name;

    /**
     * @ORM\Column(name="rte_main", type="string", length=3)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="3")
     *
     * @Serializer\Expose
     */
    private $mainCurrency;

    /**
     * @ORM\Column(name="rte_secondary", type="string", length=3)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="3")
     *
     * @Serializer\Expose
     */
    private $secondaryCurrency;

    /**
     * @ORM\Column(name="rte_rate", type="float")
     *
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     */
    private $rate;

    /**
     * @ORM\Column(name="rte_eid", type="string", length=50)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="50")
     *
     * @Serializer\Expose
     */
    private $externalId;

    /**
     * @ORM\Column(name="rte_date", type="date")
     *
     * @Assert\NotBlank
     *
     * @Serializer\Expose
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMainCurrency(): ?string
    {
        return $this->mainCurrency;
    }

    public function setMainCurrency(string $mainCurrency): self
    {
        $this->mainCurrency = $mainCurrency;

        return $this;
    }

    public function getSecondaryCurrency(): ?string
    {
        return $this->secondaryCurrency;
    }

    public function setSecondaryCurrency(string $secondaryCurrency): self
    {
        $this->secondaryCurrency = $secondaryCurrency;

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
