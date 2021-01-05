<?php

namespace App\Tests\Command;

use App\Component\DTO\Rate;
use App\Event\RateEvent;
use GuzzleHttp\Client;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @group functional
 */
class CurrencyParserCommandTest extends KernelTestCase
{
    private EventDispatcherInterface $dispatcher;

    /** @var Client|MockObject */
    private MockObject $httpClient;

    public function setUp()
    {
        self::bootKernel(['environment' => 'test']);

        $this->dispatcher = self::$container->get('event_dispatcher');
        $this->httpClient = $this->createMock(Client::class);

        // Replace HTTP Client (Guzzle) in the Service Container (set Mock instead of real service).
        self::$container->set('test.guzzle', $this->httpClient);
    }

    /**
     * Should allow to emit Rate events.
     */
    public function testShouldAllowToEmitRateEvents(): void
    {
        // Hack, we will collect all events here!
        /** @var RateEvent[] $events */
        $events = [];

        $content = \file_get_contents(__DIR__ . '/../../_data/xml/cbr_collection.xml');

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($content);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with(...['https://www.cbr.ru/scripts/XML_daily.asp'])
            ->willReturn($response)
        ;

        $this->dispatcher->addListener(RateEvent::class, function (RateEvent $event) use (&$events) {
            $events[] = $event;
        });

        $application = new Application(self::$kernel);

        $command       = $application->find('crassula:currency:parser');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            // Does not have any params and arguments.
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Success.', $output);

        $this->assertCount(2, $events);
        $this->assertInstanceOf(Rate::class, $events[0]->getRate());
    }
}
