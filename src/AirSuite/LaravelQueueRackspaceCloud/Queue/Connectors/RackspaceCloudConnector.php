<?php

namespace AirSuite\LaravelQueueRackspaceCloud\Queue\Connectors;

use AirSuite\LaravelQueueRackspaceCloud\Queue\RackspaceCloudQueue;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Queue\Connectors\ConnectorInterface;
use OpenCloud\Common\Exceptions\InvalidArgumentError;
use OpenCloud\Queues\Service;
use OpenCloud\Rackspace;

class RackspaceCloudConnector implements ConnectorInterface
{
  /**
   * @var Rackspace
   */
  protected $connection = NULL;

  /**
   * @var Service
   */
  protected $service = NULL;

  /**
   * Establish a queue connection.
   *
   * @param array $config
   *
   * @return Queue
   * @throws InvalidArgumentError
   */
  public function connect(array $config): Queue
  {
    switch ($config['endpoint'])
    {
      case 'UK':
        $endpoint = Rackspace::UK_IDENTITY_ENDPOINT;
        break;
      case 'US':
      default:
        $endpoint = Rackspace::US_IDENTITY_ENDPOINT;
    }

    if (is_null($this->connection))
    {
      $this->connection = new Rackspace(
          $endpoint,
          [
              'username' => $config['username'],
              'apiKey'   => $config['apiKey'],
          ]
      );
    }

    if (is_null($this->service))
    {
      $this->service = $this->connection->queuesService(
          NULL,
          $config['region']
      );
    }

    $this->service->setClientId();

    return new RackspaceCloudQueue($this->service, $config['queue']);
  }
}
