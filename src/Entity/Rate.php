<?php

namespace App\Entity;

use App\Repository\RateRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="rate", indexes={
 *     @ORM\Index(columns={"rte_date", "rte_source", "rte_secondary", "rte_main"})
 * })
 *
 * @ORM\Entity(repositoryClass=RateRepository::class)
 */
class Rate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="rte_id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="rte_source", type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    private $source;

    /**
     * @ORM\Column(name="rte_name", type="string", length=255)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    private $name;

    /**
     * @ORM\Column(name="rte_main", type="string", length=3)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="3")
     */
    private $mainCurrency;

    /**
     * @ORM\Column(name="rte_secondary", type="string", length=3)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="3")
     */
    private $secondaryCurrency;

    /**
     * @ORM\Column(name="rte_rate", type="float")
     *
     * @Assert\NotBlank
     */
    private $rate;

    /**
     * @ORM\Column(name="rte_eid", type="string", length=50)
     *
     * @Assert\NotBlank
     * @Assert\Length(max="50")
     */
    private $externalId;

    /**
     * @ORM\Column(name="rte_date", type="date")
     *
     * @Assert\NotBlank
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
