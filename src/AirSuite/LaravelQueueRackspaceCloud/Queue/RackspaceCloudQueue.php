<?php

namespace AirSuite\LaravelQueueRackspaceCloud\Queue;

use AirSuite\LaravelQueueRackspaceCloud\Queue\Jobs\RackspaceCloudJob;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Queue;
use OpenCloud\Common\Collection\PaginatedIterator;
use OpenCloud\Common\Constants\Datetime;
use OpenCloud\Common\Exceptions\InvalidArgumentError;
use OpenCloud\Queues\Resource\Queue as OpenCloudQueue;
use OpenCloud\Queues\Service as OpenCloudService;
use RuntimeException;

/**
 * Class RackspaceCloudQueue
 * @package AirSuite\LaravelQueueRackspaceCloud\Queue
 */
class RackspaceCloudQueue extends Queue implements QueueContract
{
  /**
   * The Rackspace OpenCloud Message Service instance.
   *
   * @var OpenCloudService
   */
  protected $openCloudService;

  /**
   * The Rackspace OpenCloud Queue instance
   *
   * @var OpenCloudQueue
   */
  protected $queue;

  /**
   * The name of the default tube.
   *
   * @var string
   */
  protected $default;

  /**
   * @param OpenCloudService $openCloudService
   * @param                  $default
   * @throws InvalidArgumentError
   */
  public function __construct(OpenCloudService $openCloudService, $default)
  {
    $this->openCloudService = $openCloudService;
    $this->default          = $default;
    $this->queue            = $this->getQueue($default);
  }

  /**
   * Get the size of the queue.
   *
   * @param string $queue
   * @return int
   */
  public function size($queue = NULL)
  {
    // TODO not implemented
    return 1;
  }

  /**
   * Push a new job onto the queue.
   *
   * @param string $job
   * @param mixed  $data
   * @param string $queue
   * @return mixed
   * @throws InvalidArgumentError
   */
  public function push($job, $data = '', $queue = NULL)
  {
    return $this->pushRaw($this->createPayload($job, $data), $queue);
  }

  /**
   * Push a raw payload onto the queue.
   *
   * @param string $payload
   * @param string $queue
   * @param array  $options
   * @return bool
   * @throws InvalidArgumentError
   */
  public function pushRaw($payload, $queue = NULL, array $options = [])
  {
    // TODO array_get is deprecated
    $ttl        = array_get($options, 'ttl', Datetime::DAY * 2);
    $cloudQueue = $this->getQueue($queue);
    return $cloudQueue->createMessage(
        [
            'body' => $payload,
            'ttl'  => $ttl,
        ]
    );
  }

  /**
   * Push a new job onto the queue after a delay.
   *
   * @param \DateTime|int $delay
   * @param string        $job
   * @param string        $data
   * @param null          $queue
   * @return mixed|void
   */
  public function later($delay, $job, $data = '', $queue = NULL)
  {
    throw new RuntimeException('RackspaceCloudQueue::later() method is not supported');
  }

  /**
   * Pop the next job off of the queue.
   *
   * @param string $queue
   * @return Job|null|RackspaceCloudJob
   * @throws InvalidArgumentError
   */
  public function pop($queue = NULL)
  {
    $cloudQueue = $this->getQueue($queue);
    /**
     * @var PaginatedIterator $response
     */
    $response = $cloudQueue->claimMessages(
        [
            'limit' => 1,
            'grace' => 5 * Datetime::MINUTE,
            'ttl'   => 5 * Datetime::MINUTE,
        ]
    );

    if ($response && $response->valid())
    {
      $message = $response->current();
      return new RackspaceCloudJob($this->container, $cloudQueue, $queue, $message);
    }

    return NULL;
  }

  /**
   * Get the queue or return the default.
   * @param $queue
   * @return OpenCloudQueue
   * @throws InvalidArgumentError
   */
  protected function getQueue($queue)
  {
    if (is_null($queue))
    {
      return $this->queue;
    }
    return $this->openCloudService->createQueue($queue);
  }
}
