<?php

namespace App\Tests\Unit\Service;

use App\Service\DeserializerProvider;
use App\Service\SourceConfiguration;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class DeserializerProviderTest extends TestCase
{
    private DeserializerProvider $provider;

    /** @var MockObject|SourceConfiguration */
    private MockObject $config;

    public function setUp()
    {
        $generator = function () {
            yield 'wrong' => null;
        };

        $this->config   = $this->createMock(SourceConfiguration::class);
        $this->provider = new DeserializerProvider($generator(), $this->config);
    }

    /**
     * Should not allow to get not loaded deserializer.
     */
    public function testShouldNotAllowToGetNotLoadedDeserializer(): void
    {
        $this->config
            ->expects($this->once())
            ->method('getDefaultSource')
            ->willReturn('cbr');

        $this->expectExceptionMessage('Deserializer: \'cbr\' has not been loaded.');
        $this->expectException(\LogicException::class);
        $this->provider->getDefault();
    }
}
