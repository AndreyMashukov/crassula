<?php

namespace App\DataFixtures;

use App\Component\DTO\CBRRate;
use App\Component\DTO\DateCollectionInterface;
use App\Component\DTO\ECBRate;
use App\Component\DTO\Rate as RateDTO;
use App\Entity\Rate;
use App\Service\DeserializerProvider;
use App\Service\SourceConfiguration;
use App\Service\SourceNameInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private DeserializerProvider $deserializerProvider;

    public function __construct(DeserializerProvider $deserializerProvider)
    {
        $this->deserializerProvider = $deserializerProvider;
    }

    public function load(ObjectManager $manager)
    {
        $ecbContent = \file_get_contents(__DIR__ . '/data/ecb.xml');
        $cbrContent = \file_get_contents(__DIR__ . '/data/cbr.xml');

        $ecbCollection = $this->deserializerProvider
            ->getBySource(SourceConfiguration::SOURCE_ECB)
            ->deserialize($ecbContent)
        ;

        /** @var ECBRate[]|RateDTO $item */
        foreach ($ecbCollection->getRates() as $item) {
            if ($ecbCollection instanceof DateCollectionInterface) {
                $item->setDate($ecbCollection->getDate());
            }

            $manager->persist($this->makeEntity($item));
        }

        $cbrCollection = $this->deserializerProvider
            ->getBySource(SourceConfiguration::SOURCE_CBR)
            ->deserialize($cbrContent);

        /** @var CBRRate[]|RateDTO $item */
        foreach ($cbrCollection->getRates() as $item) {
            if ($cbrCollection instanceof DateCollectionInterface) {
                $item->setDate($cbrCollection->getDate());
            }

            $manager->persist($this->makeEntity($item));
        }

        $manager->flush();
    }

    private function makeEntity(RateDTO $dto): Rate
    {
        $date = $dto->getDate();

        $entity = (new Rate())
            ->setName($dto->getName())
            ->setExternalId($dto->getExternalId())
            ->setMainCurrency($dto->getMainCurrency())
            ->setSecondaryCurrency($dto->getSecondaryCurrency())
            ->setRate($dto->getFinalRate())
        ;

        if ($date instanceof \DateTimeInterface) {
            $entity->setDate($dto->getDate());
        }

        if ($dto instanceof SourceNameInterface) {
            $entity->setSource($dto::getSource());
        }

        return $entity;
    }
}
