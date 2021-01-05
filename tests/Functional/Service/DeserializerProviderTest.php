<?php

namespace App\Tests\Functional\Service;

use App\Service\Deserializer\CBRDeserializer;
use App\Service\Deserializer\ECBDeserializer;
use App\Service\DeserializerProvider;
use App\Service\SourceConfiguration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group functional
 */
class DeserializerProviderTest extends KernelTestCase
{
    private DeserializerProvider $provider;

    public function setUp()
    {
        self::bootKernel(['environment' => 'test']);

        $this->provider = self::$container->get('test.deserializer_provider');
    }

    /**
     * Should allow to get deserializer by source.
     *
     * Also tags assertion here.
     */
    public function testShouldAllowToGetDeserializerBySource(): void
    {
        $this->assertInstanceOf(ECBDeserializer::class, $this->provider->getBySource(SourceConfiguration::SOURCE_ECB));
        $this->assertInstanceOf(CBRDeserializer::class, $this->provider->getBySource(SourceConfiguration::SOURCE_CBR));
    }

    /**
     * Should not allow to get not allowed deserializer.
     */
    public function testShouldNotAllowToGetNotAllowedDeserializer(): void
    {
        $this->expectExceptionMessage('Source: \'wrong\' is not allowed.');
        $this->expectException(\InvalidArgumentException::class);
        $this->provider->getBySource('wrong');
    }
}
