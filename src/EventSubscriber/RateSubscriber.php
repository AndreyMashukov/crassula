<?php

namespace App\EventSubscriber;

use App\Entity\Rate;
use App\Event\RateEvent;
use App\Service\SourceNameInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RateSubscriber implements EventSubscriberInterface
{
    private ManagerRegistry $registry;

    private ValidatorInterface $validator;

    private int $storeIterations = 0;

    private int $storeBatchSize = 100;

    private bool $debug;

    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator, bool $debug)
    {
        $this->registry  = $registry;
        $this->validator = $validator;
        $this->debug     = $debug;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            RateEvent::class => [
                ['makeEntity', 500],
                ['store', 400],
            ],
        ];
    }

    public function makeEntity(RateEvent $event): void
    {
        $dto  = $event->getRate();
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

        $event->setEntity($entity);
    }

    public function store(RateEvent $event): void
    {
        if (!$this->isValidEntityEvent($event)) {
            return;
        }

        $newEntity = $event->getEntity();
        $manager   = $this->registry->getManager();

        if (0 === ($this->storeIterations % $this->getStoreBatchSize())) {
            // Clear memory.
            $manager->clear(Rate::class);
        }

        ++$this->storeIterations;

        $entity = $manager->getRepository(Rate::class)->findOneBy([
            'date'              => $newEntity->getDate(),
            'source'            => $newEntity->getSource(),
            'secondaryCurrency' => $newEntity->getSecondaryCurrency(),
            'mainCurrency'      => $newEntity->getMainCurrency(),
        ]);

        if ($entity instanceof Rate) {
            $entity->setRate($newEntity->getRate());
            $manager->flush();

            return;
        }

        $manager->persist($newEntity);
        $manager->flush();
    }

    private function isValidEntityEvent(RateEvent $event): bool
    {
        $entity = $event->getEntity();

        if (!$entity instanceof Rate) {
            // todo logger...

            $event->stopPropagation();

            return false;
        }

        $errors = $this->validator->validate($entity, null, [
            'Default',
        ]);

        return 0 === \count($errors);
    }

    private function getStoreBatchSize(): int
    {
        if ($this->debug) {
            return 1; // for testing.
        }

        return $this->storeBatchSize;
    }
}
