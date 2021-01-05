<?php

namespace App\EventSubscriber;

use App\Event\RateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RateSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            RateEvent::class => 'onRate',
        ];
    }

    public function onRate(RateEvent $event): void
    {
        // unused. will be implemented later.
        unset($event);
    }
}
