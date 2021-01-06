<?php

namespace App\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class RestTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected EntityManagerInterface $em;

    protected function services(): void
    {
        // todo mock here services.
    }

    public function setUp(): void
    {
        $this->client = self::createClient(['environment' => 'test']);
        $this->client->disableReboot();

        $this->services();

        $this->em = self::$container->get('doctrine.orm.default_entity_manager');
        $this->em->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->em->rollback();

        parent::tearDown();
    }

    public function getUrl(string $name, array $params = []): string
    {
        return self::$container->get('router')->generate($name, $params);
    }

    public function deserialize(Response $response, int $statusCode = Response::HTTP_OK)
    {
        $content = $response->getContent();
        $this->assertEquals($statusCode, $response->getStatusCode(), $content);

        return \json_decode($content, true);
    }

    public function apiRequest(string $uri, string $method = Request::METHOD_GET, array $params = []): Response
    {
        $this->client->request($method, $uri, $params, []);

        return $this->client->getResponse();
    }

    protected function sqlProfiled(\Closure $closure, int $expectedCount, string $mode = 'assertEquals')
    {
        $this->client->enableProfiler();
        /** @var Profiler $profiler */
        $profiler  = self::$container->get('profiler');

        try {
            $profiler->get('db')->reset();
            $sqlBefore = $profiler->get('db')->getQueryCount();
        } catch (\Exception $exception) {
            unset($exception);

            $sqlBefore = 0;
        }

        $result = $closure();

        $sqlAfter = $profiler->get('db')->getQueryCount();

        $this->{$mode}($expectedCount, \abs($sqlAfter - $sqlBefore), \print_r($profiler->get('db')->getQueries(), true));

        return $result;
    }

    /**
     * @param array|string $snapshot
     * @param bool         $fixRandom
     * @param string       $ext
     * @param string       $suffix
     *
     * @throws \ReflectionException
     */
    protected function assertSnapshot(
        $snapshot,
        bool $fixRandom = false,
        string $ext = 'json',
        string $suffix = ''
    ): void {
        if ($fixRandom) {
            $snapshot = $this->assertId($snapshot);
        }

        $name       = \preg_replace('/[^a-z0-9]/ui', '', $this->getName() . $suffix);
        $projectDir = self::$container->getParameter('kernel.project_dir');
        $dataSetDir = "{$projectDir}/tests/_data/{$this->getTestPath()}";

        if (!\file_exists($dataSetDir)) {
            \mkdir($dataSetDir, 0777, true);
        }

        $filePath = "{$dataSetDir}/{$name}.{$ext}";

        if (\file_exists($filePath)) {
            $fixer = function (string $string) {
                return \preg_replace('/(\s|\b|\n)+/ui', '', $string);
            };

            $expected = \is_array($snapshot)
                ? \json_decode(\file_get_contents($filePath), true)
                : $fixer(\file_get_contents($filePath));

            $this->assertEquals($expected, \is_array($snapshot) ? $snapshot : $fixer($snapshot));

            return;
        }

        \file_put_contents($filePath, \is_array($snapshot) ? \json_encode($snapshot, JSON_UNESCAPED_UNICODE) : $snapshot);
        $this->markTestIncomplete('Snapshot has been created.');
    }

    /**
     * @param array|string $data
     *
     * @return array
     */
    private function assertId($data)
    {
        if (!\is_array($data)) {
            return $data;
        }

        $new = [];

        foreach ($data as $key => $item) {
            if (\is_array($item)) {
                $new[$key] = $this->assertId($item);

                continue;
            }

            if ('username' === $key) {
                $new[$key] = '<username>';

                continue;
            }

            if (false !== \mb_strpos($key, 'date')) {
                $new[$key] = '<date>';

                continue;
            }

            $datePattern = '/^20[2-9][0-9]-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}\+[0-9]{2}:[0-9]{2}$/ui';

            if (\preg_match($datePattern, $item)) {
                $new[$key] = '<date>';

                continue;
            }

            if (
            \preg_match(
                '/^(?P<first>http(s)?:\/\/[a-z0-9-\.]+(net|ru|com|svt|test))(?P<second>.*)$/ui',
                $item,
                $matches
            )
            ) {
                $new[$key] = "host{$matches['second']}";

                continue;
            }

            if (!\preg_match('/^([a-z]+Id|id)$/u', $key) && !\preg_match('/^[a-z0-9-]{36}$/ui', $item)) {
                $new[$key] = $item;

                continue;
            }

            if (null === $item) {
                $new[$key] = '<id>';

                continue;
            }

            if (!\is_int($item) && 36 !== \mb_strlen($item)) {
                $new[$key] = $item;

                continue;
            }

            if (!\is_int($item)) {
                $this->assertRegExp('/^[a-z0-9-]{36}$/ui', $item);

                $new[$key] = '<id>';

                continue;
            }

            $this->assertInternalType('integer', $item);

            $new[$key] = '<id>';
        }

        return $new;
    }

    /**
     * @throws \ReflectionException
     *
     * @return string
     */
    protected function getTestPath(): string
    {
        $reflection = new \ReflectionClass(\get_class($this));

        if (false === \preg_match('/tests\/(?P<path>.*)\.php$/ui', $reflection->getFileName(), $matches)) {
            throw new \RuntimeException("Can't get path!");
        }

        return $matches['path'];
    }
}
