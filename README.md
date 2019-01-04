# Rubix Model Server
Put your [Rubix ML](https://github.com/RubixML/RubixML) models live into production fast and effortlessly using real-time standalone model servers based on [React PHP](https://reactphp.org/).

## Installation
Install Rubix Server using Composer:

```sh
$ composer require rubix/server
```

## Requirements
-  [PHP](https://php.net/manual/en/install.php) 7.1.3 or above

## Documentation

### Table of Contents
- [Getting Started](#getting-started)
- [Servers](#servers)
	- [REST Server](#rest-server)
- [Middleware](#middeware)
	- [Shared Token Authenticator](#shared-token-authenticator)

---
### Getting Started
Once you have trained and fine-tuned a machine learning model for a live system, the next step is to put it into production. Rubix model servers expose your trained models as standalone services (such as REST) that can be queried in a live production environment. 

#### Example
```php
use Rubix\Server\RESTServer;
use Rubix\ML\Classifiers\KNearestNeighbors;

$estimator = new KNearestNeighbors(5);

// Train estimator

$server = new RESTServer([
    '/example' => $estimator,
]);

$server->run();
```
Or you could load a previously trained model from storage using the [Persistent Model](https://github.com/RubixML/RubixML#persistent-model) meta-Estimator and then serve it.

```php
use Rubix\ML\PersistentModel;
use Rubix\ML\Persisters\Filesystem;

$estimator = PersistentModel::load(new  Filesystem('sentiment.model'));

$server = new RESTServer([
    '/sentiment' => $estimator,
]);

$server->run();
```

---
### Servers
Server objects are standalone server implementations built on React PHP, an event-driven system that makes it possible to serve concurrent requests to the same model in memory. Each server implements its own network stack (TCP, HTTP, etc.) such that they can be run from without the need for additional components such as Nginx or Apache.

To boot up a server, simply call the `run()` method on the instance.
```php
public function run() : void
```
The server will stay running until the process is terminated.

> **Note**: It is a good practice to use a process monitor such as [Supervisor](http://supervisord.org/) to start and autorestart the server in case there is a failure.


### REST Server
Representational State Transfer (REST) server over HTTP and HTTPS where each model (*resource*) is given a unique user-specified URI prefix.

#### Parameters:

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | mapping | | array | The mapping of URI prefix (array key) to estimator instance using an associative array. |
| 2 | middleware | None| array | The HTTP middleware stack to run on each request. |
| 3 | host | '127.0.0.1' | string | The host address to bind the server to. |
| 4 | port | 8888 | int | The port to run the HTTP services on. |
| 5 | cert | None | string | The path to the certificate used to authenticate and encrypt the HTTP channel. |

#### Example
```php
use Rubix\Server\RESTServer;
use Rubix\Server\Middleware\SharedTokenAuthenticator;

$server = new RESTServer([
    '/sentiment' => $sentiment,
    '/credit' => $credit,
    '/housing' => $housing,
], [
    new SharedTokenAuthenticator('secret'),
], '127.0.0.1', 4443, '/cert.pem');
```

---
### Middleware
On the map ...

### Shared Token Authenticator

On the map ...
