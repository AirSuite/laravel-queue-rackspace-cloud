<?php

namespace AirSuite\LaravelQueueRackspaceCloud\Queue\Jobs;

use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Jobs\JobName;
use OpenCloud\Queues\Exception\DeleteMessageException;
use OpenCloud\Queues\Resource\Message;
use OpenCloud\Queues\Resource\Queue as OpenCloudQueue;
use RuntimeException;

class RackspaceCloudJob extends Job implements JobContract
{
  /**
   * The Rackspace OpenCloud Queue instance.
   *
   * @var OpenCloudQueue
   */
  protected $openCloudQueue;

  /**
   * The message instance.
   *
   * @var Message
   */
  protected $message;

  /**
   * @param Container      $container
   * @param OpenCloudQueue $openCloudQueue
   * @param                $queue
   * @param Message        $message
   */
  public function __construct(Container $container, OpenCloudQueue $openCloudQueue, $queue, Message $message)
  {
    $this->openCloudQueue = $openCloudQueue;
    $this->message        = $message;
    $this->queue          = $queue;
    $this->container      = $container;
  }

  /**
   * Fire the job.
   *
   */
  public function fire()
  {
    $payload = $this->payload();
    [ $class, $method ] = JobName::parse($payload['job']);
    with($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
  }

  /**
   * Release the job back into the queue.
   *
   * @param int $delay
   * @throws DeleteMessageException
   */
  public function release($delay = 0)
  {
    $this->message->delete($this->message->getClaimIdFromHref());
  }

  /**
   * Get the number of times the job has been attempted.
   *
   * @throws RuntimeException
   */
  public function attempts()
  {
    throw new RuntimeException('RackspaceCloudQueueJob::attempts() is unsupported');
  }

  /**
   * Get the raw body string for the job.
   *
   * @return string
   */
  public function getRawBody()
  {

    /** @noinspection PhpUndefinedMethodInspection It's a magic method */
    return $this->message->getBody();
  }

  /**
   * Delete the job from the queue.
   *
   */
  public function delete()
  {
    parent::delete();
    /** @noinspection PhpUndefinedMethodInspection It's a magic method */
    $this->openCloudQueue->deleteMessages([ $this->message->getId() ]);
  }

  /**
   * Get the job identifier.
   *
   * @return string
   */
  public function getJobId()
  {
    /** @noinspection PhpUndefinedMethodInspection It's a magic method */
    return $this->message->getId();
  }
}
