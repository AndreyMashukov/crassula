<?php

namespace App\Tests\Functional\Service\Deserializer;

use App\Component\DTO\CBRCollection;
use App\Component\DTO\CBRRate;
use App\Service\Deserializer\CBRDeserializer;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group functional
 */
class CBRDeserializerTest extends KernelTestCase
{
    private SerializerInterface $serializer;

    private CBRDeserializer $deserializer;

    public function setUp()
    {
        self::bootKernel(['environment' => 'test']);

        $this->serializer   = self::$container->get('jms_serializer');
        $this->deserializer = self::$container->get('test.cbr_deserializer');
    }

    /**
     * Should allow to deserialize one Unit.
     *
     * Annotations test.
     */
    public function testShouldAllowToDeserializeOneUnit(): void
    {
        $content = \file_get_contents(__DIR__ . '/../../../_data/xml/cbr_unit.xml');

        /** @var DeserializationContext $context */
        $context = DeserializationContext::create();
        $context->setGroups(['Default']);

        $dto = $this->serializer->deserialize($content, CBRRate::class, 'xml', $context);

        $this->assertEquals('R01535', $dto->getExternalId());
        $this->assertEquals('RUB', $dto->getMainCurrency());
        $this->assertEquals('NOK', $dto->getSecondaryCurrency());
        $this->assertEquals(10, $dto->getNominal());
        $this->assertEquals(86.0, $dto->getRate());
        $this->assertEquals('Норвежских крон', $dto->getName());
    }

    /**
     * Should allow to deserialize Collection.
     *
     * Annotations test.
     *
     * @return CBRCollection
     */
    public function testShouldAllowToDeserializeCollection(): CBRCollection
    {
        $content = \file_get_contents(__DIR__ . '/../../../_data/xml/cbr_collection.xml');

        /** @var DeserializationContext $context */
        $context = DeserializationContext::create();
        $context->setGroups(['Default']);

        /** @var CBRCollection $collection */
        $collection = $this->serializer->deserialize($content, CBRCollection::class, 'xml', $context);

        $this->assertInstanceOf(CBRCollection::class, $collection);
        $this->assertEquals(5, $collection->getRates()->count());
        $first = $collection->getRates()->first();
        $this->assertInstanceOf(CBRRate::class, $first);

        $this->assertEquals('R01535', $first->getExternalId());
        $this->assertEquals('RUB', $first->getMainCurrency());
        $this->assertEquals('NOK', $first->getSecondaryCurrency());
        $this->assertEquals(10, $first->getNominal());
        $this->assertEquals(86.0, $first->getRate());
        $this->assertEquals('Норвежских крон', $first->getName());

        return $collection;
    }

    /**
     * Should allow to deserialize list like in previous tests.
     *
     * @depends testShouldAllowToDeserializeCollection
     *
     * @param CBRCollection $expected
     */
    public function testDeserializer(CBRCollection $expected): void
    {
        $content = \file_get_contents(__DIR__ . '/../../../_data/xml/cbr_collection.xml');

        /** @var CBRCollection $collection */
        $collection = $this->deserializer->deserialize($content);
        $this->assertEquals($expected, $collection);

        $this->assertEquals('01.01.2021', $collection->getDate()->format('d.m.Y'));
    }
}
