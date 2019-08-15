<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Tests;

use LazyHttpClientBundle\Client\HttpQueue;
use LazyHttpClientBundle\Interfaces\CacheAdapterInterface;
use LazyHttpClientBundle\Profiler\RequestCollector;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class MockedHttpQueue
 */
class MockedHttpQueue extends HttpQueue
{
    /**
     * Directory where mocked files are saving
     */
    private const MOCK_DIRECTORY = 'var'.DIRECTORY_SEPARATOR.'lazy_http_client'.DIRECTORY_SEPARATOR;

    private const MOCK_EXTENSION = 'response';

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * MockedHttpQueue constructor.
     *
     * @param LoggerInterface            $logger
     * @param RequestCollector           $requestCollector
     * @param CacheAdapterInterface|null $cacheAdapter
     * @param KernelInterface            $kernel
     *
     * @throws \LazyHttpClientBundle\Exception\BadCacheAdapterException
     */
    public function __construct(
        LoggerInterface $logger,
        RequestCollector $requestCollector,
        ?CacheAdapterInterface $cacheAdapter,
        KernelInterface $kernel
    ) {
        parent::__construct($logger, $requestCollector, $cacheAdapter);
        $this->kernel = $kernel;
    }

    public function execute(): void
    {
        $mockDirectory = $this->kernel->getProjectDir().DIRECTORY_SEPARATOR.static::MOCK_DIRECTORY;

        // Create a directory or remove file and create the directory
        if (!\file_exists($mockDirectory)) {
            if (!mkdir($mockDirectory, 0777, true) && !is_dir($mockDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $mockDirectory));
            }
        } elseif (!\is_dir($mockDirectory)) {
            \unlink($mockDirectory);
            if (!mkdir($mockDirectory, 0777, true) && !is_dir($mockDirectory)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $mockDirectory));
            }
        }

        // Fill responses with exists mocked data
        $existsKeys = [];
        foreach ($this->pool as $key => $query) {
            $fileName = $mockDirectory.$key.'.'.static::MOCK_EXTENSION;
            if (\file_exists($fileName)) {
                $this->responses[$key] = \unserialize(\file_get_contents($fileName));
                $existsKeys[] = $key;
            }
        }

        parent::execute();

        // Put new responses to mock files
        foreach ($this->responses as $key => $response) {
            $fileName = $mockDirectory.$key.'.'.static::MOCK_EXTENSION;
            if (!\in_array($key, $existsKeys)) {
                \file_put_contents($fileName, \serialize($response));
            }
        }
    }
}
