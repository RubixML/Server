# Rubix Model Server
High-performance standalone model servers bring your [Rubix ML](https://github.com/RubixML/RubixML) estimators live into production quickly and effortlessly.

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
- [Clients](#clients)
	- [REST Client](#rest-client)
- [Http Middleware](#http-middeware)
	- [Shared Token Authenticator](#shared-token-authenticator)

---
### Getting Started
Once you have trained a machine learning model for a live system, the next step is to put it into production with one of the model servers. Rubix model servers expose your trained models as standalone services (such as REST) that can be queried in a live environment.

---
### Servers
Server objects are standalone server implementations built on top of React PHP, an event-driven system that makes it possible to serve thousands of concurrent requests at once.

To boot up a server, simply call the `run()` method on the instance.
```php
public function run() : void
```

#### Example
```php
use Rubix\Server\RESTServer;
use Rubix\ML\Classifiers\KNearestNeighbors;

$estimator = new KNearestNeighbors(3);

// Train estimator

$server = new RESTServer($estimator);

$server->run();
```

The server will stay running until the process is terminated.

> **Note**: It is a good practice to use a process monitor such as [Supervisor](http://supervisord.org/) to start and autorestart the server in case there is a failure.

### REST Server
JSON based Representational State Transfer (REST) server over HTTP and HTTPS.

> **Note**: This server implements its own networking stack (TCP, HTTP, etc.) so that it can be run without the need for additional networking components such as Nginx or Apache.

#### Parameters:
| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | estimator | | object | The estimator instance that you want to serve. |
| 2 | middleware | None| array | The HTTP middleware stack to run on each request. |
| 3 | host | '127.0.0.1' | string | The host address to bind the server to. |
| 4 | port | 8888 | int | The network port to run the HTTP services on. |
| 5 | cert | null | ?string | The path to the certificate used to authenticate and encrypt the HTTP channel. |

#### Routes:
| Method | URI | JSON Params | Description |
|--|--|--|--|
| GET | /model | | Query information about the model. |
| POST | /model/predictions | `samples` | Return the predictions given by the model. |
| POST | /model/probabilities | `samples` | Predict the probabilities of each outcome. |
| GET | /server/status | | Query the status of the server. |

#### Example
```php
use Rubix\Server\RESTServer;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use Rubix\ML\Classifiers\KNearestNeighbors;

$estimator = new KNearestNeighbors(3);

// Train the estimator

$server = new RESTServer($estimator, [
    new SharedTokenAuthenticator('secret'),
], '127.0.0.1', 4443, '/cert.pem');
```

---
### Clients
Clients allow you to communicate with a server over the wire using a user friendly object-oriented interface. Each client is capable of sending *commands* to the backend server with the `send()` method while handling all of the networking under the hood.

To send a Command and return its results:
```php
public send(Command $command) : array
```

#### Example:
```php
use Rubix\Server\RESTClient;
use Rubix\Server\Commands\Predict;

$client = new RESTClient('127.0.0.1', 8888);

$result = $client->send(new Predict($samples));
```

### REST Client
The REST Client is made to communicate with a [REST Server](#rest-server) over HTTP or Secure HTTP (HTTPS).

#### Parameters:
| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | host | '127.0.0.1' | string | The address of the server. |
| 2 | port | 8888 | int | The network port that the HTTP server is running on. |
| 3 | secure | false | bool | Should we use HTTPS?. |
| 4 | headers | None| array | Any additional HTTP headers to send along with each request. |

#### Example:
```php
use Rubix\Server\RESTClient;

$client = new RESTClient('127.0.0.1', 8888, false, [
    'Authorization' => 'secret',
]);
```

---
### HTTP Middleware
HTTP middleware are objects that process incoming HTTP requests before they are handled. 

### Shared Token Authenticator
Authenticates incoming requests using a shared key that is kept secret between the client and server.

> **Note**: This strategy is only secure over an encrypted channel such as HTTPS with SSL or TLS.

#### Parameters:
| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | token | | string | The shared secret key (token) required to authenticate every request. |

#### Example
```php
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;

$middleware = new SharedTokenAuthenticator('secret');
```

---
## Testing
Rubix utilizes a combination of static analysis and unit tests for quality assurance and to reduce the number of bugs. Rubix provides two [Composer](https://getcomposer.org/) scripts that can be run from the root directory to automate the testing process.

To run static analysis:
```sh
composer analyze
```

To run the unit tests:
```sh
composer test
```

---
## License
[MIT](https://github.com/RubixML/Server/blob/master/LICENSE.md)