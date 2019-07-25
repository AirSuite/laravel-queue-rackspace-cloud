<?php

namespace AirSuite\LaravelQueueRackspaceCloud;

use AirSuite\LaravelQueueRackspaceCloud\Queue\Connectors\RackspaceCloudConnector;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;

class LaravelQueueRackspaceCloudServiceProvider extends ServiceProvider
{
  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(
        __DIR__ . '/../../config/rackspace_cloud.php', 'queue.connections.RackspaceCloud'
    );
  }

  /**
   * Register the application's event listeners.
   *
   * @return void
   */
  public function boot()
  {
    /**
     * @var QueueManager $manager
     */
    $manager = $this->app['queue'];
    $manager->addConnector('RackspaceCloud', function ()
    {
      return new RackspaceCloudConnector;
    });
  }
}
