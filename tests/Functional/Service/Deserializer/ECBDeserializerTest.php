<?php

namespace App\Tests\Functional\Service\Deserializer;

use App\Component\DTO\ECBCollection;
use App\Component\DTO\ECBContainer;
use App\Component\DTO\ECBEnvelope;
use App\Component\DTO\ECBRate;
use App\Service\Deserializer\ECBDeserializer;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group functional
 */
class ECBDeserializerTest extends KernelTestCase
{
    private SerializerInterface $serializer;

    private ECBDeserializer $deserializer;

    public function setUp()
    {
        self::bootKernel(['environment' => 'test']);

        $this->serializer   = self::$container->get('jms_serializer');
        $this->deserializer = self::$container->get('test.ecb_deserializer');
        self::$container->get('test.deserializer_provider');
    }

    /**
     * Should allow to deserialize one Unit.
     *
     * Annotations test.
     */
    public function testShouldAllowToDeserializeOneUnit(): void
    {
        $content = \file_get_contents(__DIR__ . '/../../../_data/xml/ecb_unit.xml');

        /** @var DeserializationContext $context */
        $context = DeserializationContext::create();
        $context->setGroups(['Default']);

        $dto = $this->serializer->deserialize($content, ECBRate::class, 'xml', $context);

        $this->assertEquals('USD', $dto->getExternalId());
        $this->assertEquals('EUR', $dto->getMainCurrency());
        $this->assertEquals('USD', $dto->getSecondaryCurrency());
        $this->assertEquals(1, $dto->getNominal());
        $this->assertEquals(1.2271, $dto->getRate());
        $this->assertEquals('USD', $dto->getName());
    }

    /**
     * Should allow to deserialize Collection.
     *
     * Annotations test.
     *
     * @return ECBCollection
     */
    public function testShouldAllowToDeserializeCollection(): ECBCollection
    {
        $content = \file_get_contents(__DIR__ . '/../../../_data/xml/ecb_collection.xml');

        /** @var DeserializationContext $context */
        $context = DeserializationContext::create();
        $context->setGroups(['Default']);

        /** @var ECBCollection $collection */
        $collection = $this->serializer->deserialize($content, ECBCollection::class, 'xml', $context);

        $this->assertInstanceOf(ECBCollection::class, $collection);
        $this->assertEquals(2, $collection->getRates()->count());
        $first = $collection->getRates()->first();
        $this->assertInstanceOf(ECBRate::class, $first);

        $this->assertEquals('USD', $first->getExternalId());
        $this->assertEquals('EUR', $first->getMainCurrency());
        $this->assertEquals('USD', $first->getSecondaryCurrency());
        $this->assertEquals(1, $first->getNominal());
        $this->assertEquals(1.2271, $first->getRate());
        $this->assertEquals('USD', $first->getName());

        return $collection;
    }

    /**
     * Should allow to deserialize Envelope.
     *
     * @depends testShouldAllowToDeserializeCollection
     *
     * @param ECBCollection $expected
     */
    public function testShouldAllowToDeserializeEnvelope(ECBCollection $expected): void
    {
        $content = \file_get_contents(__DIR__ . '/../../../_data/xml/ecb_envelope.xml');

        /** @var DeserializationContext $context */
        $context = DeserializationContext::create();
        $context->setGroups(['Default']);

        /** @var ECBEnvelope $envelope */
        $envelope  = $this->serializer->deserialize($content, ECBEnvelope::class, 'xml', $context);
        $container = $envelope->getContainer();
        $this->assertInstanceOf(ECBContainer::class, $container);
        $this->assertEquals($expected, $container->getCollection());
    }

    /**
     * Should allow to deserialize list like in previous tests.
     *
     * @depends testShouldAllowToDeserializeCollection
     *
     * @param ECBCollection $expected
     */
    public function testDeserializer(ECBCollection $expected): void
    {
        $content = \file_get_contents(__DIR__ . '/../../../_data/xml/ecb_envelope.xml');

        /** @var ECBCollection $collection */
        $collection = $this->deserializer->deserialize($content);
        $this->assertEquals($expected, $collection);

        $this->assertEquals('2021-01-05', $collection->getDate()->format('Y-m-d'));
    }
}
