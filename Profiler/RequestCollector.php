<?php
declare(strict_types=1);

namespace LazyHttpClientBundle\Profiler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class RequestCollector
 */
class RequestCollector extends DataCollector
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * Collects data for the given Request and Response.
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // stub
    }

    /**
     * @param array $requestInformation
     */
    public function collectInfo(array $requestInformation)
    {
        $this->data[] = $requestInformation;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'lazy_http_client';
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->info = [];
    }

    public function getTotalTime()
    {
        $totalTime = 0;
        foreach ($this->data as $request) {
            $totalTime += $request['timing'] ?? 0;
        }

        return $totalTime;
    }
}
