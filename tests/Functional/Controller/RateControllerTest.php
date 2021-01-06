<?php

namespace App\Tests\Functional\Controller;

use App\Service\DateFactory;
use App\Service\SourceConfiguration;
use App\Tests\Functional\RestTestCase;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group functional
 */
class RateControllerTest extends RestTestCase
{
    private string $source = SourceConfiguration::SOURCE_CBR;

    protected function services(): void
    {
        $fakeDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-01-06 00:00:00');

        $factory = $this->createMock(DateFactory::class);
        $factory
            ->method('getTodayDate')
            ->willReturn($fakeDate);

        self::$container->set('test.date_factory', $factory);

        $sourceClosure = $this->getSourceClosure();

        $testSourceConfig = new class($sourceClosure) extends SourceConfiguration {
            private \Closure $closure;

            public function __construct(\Closure $closure)
            {
                parent::__construct('');

                $this->closure = $closure;
            }

            public function getDefaultSource(): string
            {
                return \call_user_func($this->closure);
            }
        };

        self::$container->set('test.source_configuration', $testSourceConfig);
    }

    private function getSourceClosure(): \Closure
    {
        return function () {
            return $this->source;
        };
    }

    /**
     * Should allow to get list by date.
     *
     * @throws \Exception
     */
    public function testShouldAllowToGetList(): void
    {
        // CBR
        $this->source = SourceConfiguration::SOURCE_CBR;

        $url      = $this->getUrl('app_rate_getlist');
        $response = $this->apiRequest($url);
        $json     = $this->deserialize($response);

        $this->assertSnapshot($json, true, 'json', '_cbr');

        // ECB
        $this->source = SourceConfiguration::SOURCE_ECB;

        $url      = $this->getUrl('app_rate_getlist');
        $response = $this->apiRequest($url);
        $json     = $this->deserialize($response);

        $this->assertSnapshot($json, true, 'json', '_ecb');
    }

    /**
     * Should allow to convert currency.
     *
     * @throws \Exception
     */
    public function testShouldAllowToConvertCurrency(): void
    {
        // CBR
        $this->source = SourceConfiguration::SOURCE_CBR;

        $response = $this->apiRequest($this->getUrl('app_rate_get'), Request::METHOD_GET, [
            'currencyFrom' => 'RUB',
            'currencyTo'   => 'EUR',
            'date'         => '2021-01-06',
            'amount'       => 100,
        ]);

        $json = $this->deserialize($response);
        $this->assertSnapshot($json, true, 'json', '_cbr');

        $response = $this->apiRequest($this->getUrl('app_rate_get'), Request::METHOD_GET, [
            'currencyFrom' => 'USD',
            'currencyTo'   => 'EUR',
            'date'         => '2021-01-06',
            'amount'       => 100,
        ]);

        $json = $this->deserialize($response);
        $this->assertSnapshot($json, true, 'json', '_cbr_usd');

        // ECB
        $this->source = SourceConfiguration::SOURCE_ECB;

        $response = $this->apiRequest($this->getUrl('app_rate_get'), Request::METHOD_GET, [
            'currencyFrom' => 'EUR',
            'currencyTo'   => 'RUB',
            'date'         => '2021-01-06',
            'amount'       => 100,
        ]);

        $json = $this->deserialize($response);
        $this->assertSnapshot($json, true, 'json', '_ecb');

        $response = $this->apiRequest($this->getUrl('app_rate_get'), Request::METHOD_GET, [
            'currencyFrom' => 'USD',
            'currencyTo'   => 'EUR',
            'date'         => '2021-01-06',
            'amount'       => 100,
        ]);

        $json = $this->deserialize($response);
        $this->assertSnapshot($json, true, 'json', '_ecb_usd');
    }

    /**
     * Should allow to handle errors.
     *
     * @throws \Exception
     */
    public function testShouldAllowToHandleErrors(): void
    {
        // CBR
        $this->source = SourceConfiguration::SOURCE_CBR;

        $response = $this->apiRequest($this->getUrl('app_rate_get'), Request::METHOD_GET, [
            'currencyFrom' => 'RUB',
            'currencyTo'   => 'UNKNOWN',
            'date'         => '2021-01-06',
            'amount'       => 100,
        ]);

        $json = $this->deserialize($response, Response::HTTP_BAD_REQUEST);
        $this->assertSnapshot($json, true, 'json', '_wrong_parameter');

        $response = $this->apiRequest($this->getUrl('app_rate_get'), Request::METHOD_GET, [
            'currencyFrom' => '',
            'currencyTo'   => '',
            'date'         => '',
        ]);

        $json = $this->deserialize($response, Response::HTTP_BAD_REQUEST);
        $this->assertSnapshot($json, true, 'json', '_validation');
    }
}
