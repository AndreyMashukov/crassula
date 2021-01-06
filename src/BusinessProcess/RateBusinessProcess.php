<?php

namespace App\BusinessProcess;

use App\Component\DTO\DateCollectionInterface;
use App\Component\DTO\Rate;
use App\Event\RateEvent;
use App\Service\DeserializerProvider;
use App\Service\SourceConfiguration;
use App\Service\SourceProvider;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class RateBusinessProcess
{
    private SourceConfiguration $sourceConfiguration;

    private SourceProvider $sourceProvider;

    private DeserializerProvider $deserializerProvider;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        SourceConfiguration $sourceConfiguration,
        SourceProvider $sourceProvider,
        DeserializerProvider $deserializerProvider,
        EventDispatcherInterface $dispatcher
    ) {
        $this->sourceConfiguration  = $sourceConfiguration;
        $this->sourceProvider       = $sourceProvider;
        $this->deserializerProvider = $deserializerProvider;
        $this->dispatcher           = $dispatcher;
    }

    public function emitRates(): void
    {
        $sourceName = $this->sourceConfiguration->getDefaultSource();
        $source     = $this->sourceProvider->getBySource($sourceName);

        $collection = $this->deserializerProvider
            ->getBySource($sourceName)
            ->deserialize($source->getRaw());

        /** @var Rate $rate */
        foreach ($collection->getRates() as $rate) {
            if ($collection instanceof DateCollectionInterface) {
                $rate->setDate($collection->getDate());
            }

            $this->dispatcher->dispatch(new RateEvent($rate));
        }
    }
}
