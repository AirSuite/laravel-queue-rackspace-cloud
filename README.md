RackspaceCloud Queue Driver for Laravel/Lumen
======================
## Installation

You can install this package via composer.json:

```
  "repositories": [
    {
      "url": "https://github.com/AirSuite/laravel-queue-rackspace-cloud.git",
      "type": "git"
    }
  ],
  "require"          : {
      "air-suite/laravel-queue-rackspace-cloud": "^1.1.0"
    },
```

Add these lines to your `app/config/queue.php` file to `connections` array:

	'RackspaceCloud' => [
		'driver' => 'RackspaceCloud',
		'queue' => env('RACKSPACECLOUD_QUEUE', 'default_tube'), // The default queue
		'endpoint' => env('RACKSPACECLOUD_ENDPOINT', 'US'), // US or UK
		'username' => env('RACKSPACECLOUD_USERNAME', 'guest'), // Some Rackspace Cloud username
		'apiKey' => env('RACKSPACECLOUD_APIKEY', '1234'), // Some Rackspace Cloud api
		'region' => env('RACKSPACECLOUD_REGION', 'ORD'), // The region the queue is setup
		'urlType'  => env('RACKSPACECLOUD_URLTYPE', 'internalURL'), // Optional, defaults to internalURL
	],

And add these properties to `.env` with proper values:

	RACKSPACECLOUD_QUEUE=name_of_the_queue
	RACKSPACECLOUD_ENDPOINT=your_rackspace_endpoint
	RACKSPACECLOUD_USERNAME=your_rackspace_username
	RACKSPACECLOUD_APIKEY=your_rackspace_api_key
	RACKSPACECLOUD_REGION=your_rackspace_region

	QUEUE_CONNECTION=RackspaceCloud

You can also find full examples in src/examples folder.

## Lumen Usage

For Lumen usage the service provider should be registered manually as follow in `bootstrap/app.php`:

```php
$app->register(YonderWeb\LaravelQueueRackspaceCloud\LaravelQueueRackspaceCloudServiceProvider::class);
```

####Attribution

This package has been guided and inspired by:
* https://github.com/vladimir-yuldashev/laravel-queue-rabbitmq
* https://github.com/tailwind/laravel-rackspace-cloudqueue
* https://github.com/davidlonjon/laravel-queue-rackspace-cloud
