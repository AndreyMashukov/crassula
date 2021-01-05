<?php

namespace App\Tests\Unit\Service;

use App\Service\DeserializerProvider;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 */
class DeserializerProviderTest extends TestCase
{
    private DeserializerProvider $provider;

    public function setUp()
    {
        $generator = function () {
            yield 'wrong' => null;
        };

        $this->provider = new DeserializerProvider($generator(), 'cbr');
    }

    /**
     * Should not allow to get not loaded deserializer.
     */
    public function testShouldNotAllowToGetNotLoadedDeserializer(): void
    {
        $this->expectExceptionMessage('Deserializer: \'cbr\' has not been loaded.');
        $this->expectException(\LogicException::class);
        $this->provider->getDefault();
    }
}
