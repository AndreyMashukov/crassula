<?php

namespace App\Tests\Command;

use App\Component\DTO\ConverterRequest;
use App\Component\DTO\Rate;
use App\Entity\Rate as Entity;
use App\Event\RateEvent;
use App\Exception\CurrencyConverterException;
use App\Service\CurrencyConverter;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Monolog\DateTimeImmutable;
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

    private EntityManagerInterface $em;

    private CurrencyConverter $converter;

    public function setUp()
    {
        self::bootKernel(['environment' => 'test']);

        $this->dispatcher = self::$container->get('event_dispatcher');
        $this->converter  = self::$container->get('test.currency_converter');
        $this->httpClient = $this->createMock(Client::class);

        // Replace HTTP Client (Guzzle) in the Service Container (set Mock instead of real service).
        self::$container->set('test.guzzle', $this->httpClient);

        $this->em = self::$container->get('doctrine.orm.default_entity_manager');

        $this->em->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    /**
     * Should allow to emit Rate events.
     */
    public function testShouldAllowToEmitRateEvents(): void
    {
        $before = $this->em->getRepository(Entity::class)->count([]);

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

        $this->assertCount(5, $events);
        $this->assertInstanceOf(Rate::class, $events[0]->getRate());
        $after = $this->em->getRepository(Entity::class)->count([]);

        $this->assertEquals(4, $after - $before);

        // Check correct Rate.

        /** @var Entity $rate */
        $rate = $this->em->getRepository(Entity::class)->findOneBy([
            'mainCurrency'      => 'RUB',
            'secondaryCurrency' => 'JPY',
        ]);

        $this->assertEquals(0.71, $rate->getRate());

        /** @var Entity $rate */
        $rate = $this->em->getRepository(Entity::class)->findOneBy([
            'mainCurrency'      => 'RUB',
            'secondaryCurrency' => 'TJS',
        ]);

        $this->assertEquals(6.5, $rate->getRate());

        $converterRequest = new ConverterRequest(
            'RUB',
            'JPY',
            100,
            DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01')
        );

        // Check conversion.
        $response = $this->converter->convert($converterRequest);

        // 100 RUB = 140.84507042254 JPY
        $this->assertEquals(140.84507042254, $response->getAmount());
        $this->assertEquals($response->getRequest(), $converterRequest);

        $converterRequest = new ConverterRequest(
            'JPY',
            'TJS',
            100,
            DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01')
        );

        // Check conversion.
        $response = $this->converter->convert($converterRequest);

        // 100 JPY = 140.84507042254 TJS
        $this->assertEquals(10.923076923077, $response->getAmount());
        $this->assertEquals($response->getRequest(), $converterRequest);

        $converterRequest = new ConverterRequest(
            'TJS',
            'NOK',
            100,
            DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01')
        );

        // Check conversion.
        $response = $this->converter->convert($converterRequest);

        // 100 TJS = 140.84507042254 NOK
        $this->assertEquals(75.581395348837, $response->getAmount());
        $this->assertEquals($response->getRequest(), $converterRequest);

        $this->expectException(CurrencyConverterException::class);
        $this->expectExceptionMessage('Unable to convert, request data is not found.');

        $converterRequest = new ConverterRequest(
            'TJS',
            'UNKNOWN',
            100,
            DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01')
        );
        $this->converter->convert($converterRequest);
    }
}
