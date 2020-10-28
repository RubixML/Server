# Rubix Server
Put your [Rubix ML](https://github.com/RubixML/RubixML) models to work by serving them with one of our high-performance stand-alone model servers that can be run from the PHP command line interface (CLI). Model severs unleash your trained estimators on the world by wrapping them in an API such as REST or RPC that can be queried over a network in real-time. Need more inference throughput? Model servers scale linearly by adding more instances behind a load balancer. Since model servers implement their own networking stack, each instance is completely self-sufficient.

## Installation
Install Rubix Server using [Composer](https://getcomposer.org/):

```sh
$ composer require rubix/server
```

## Requirements
- [PHP](https://php.net/manual/en/install.php) 7.2 or above

#### Optional
- [Event extension](https://pecl.php.net/package/event) for high volume servers
- [Bzip2 extension](https://www.php.net/manual/en/book.bzip2.php) for Bzip2 compression
- [Igbinary extension](https://github.com/igbinary/igbinary) for binary message serialization

## Documentation

### Table of Contents
- [Getting Started](#getting-started)
- [Servers](#servers)
	- [REST Server](#rest-server)
	- [RPC Server](#rpc-server)
- [Clients](#clients)
	- [RPC Client](#rpc-client)
- [HTTP Middleware](#http-middeware)
	- [Access Log](#access-log)
	- [Basic Authenticator](#basic-authenticator)
	- [Response Time](#response-time)
	- [Shared Token Authenticator](#shared-token-authenticator)
	- [Trusted Clients](#trusted-clients)
- [Messages](#messages)
	- [Commands](#commands)
		- [Predict](#predict)
		- [Predict Sample](#predict-sample)
		- [Proba](#proba)
		- [Proba Sample](#proba-sample)
		- [Score](#score)
		- [Score Sample](#score-sample)
	- [Responses](#responses)
        - [Error Response](#error-response)
        - [Predict Response](#predict-response)
		- [Predict Sample Response](#predict-sample-response)
        - [Proba Response](#proba-response)
		- [Proba Sample Response](#proba-sample-response)
        - [Score Response](#score-response)
		- [Score Sample Response](#score-sample-response)

---
### Getting Started
Once you've trained a learner in Rubix ML, the next step is to use it to make predictions. If the model is going to used to make predictions in real-time (as opposed to offline) then you'll need to make it available to clients through some type of *server*. Rubix ML model servers expose your estimators as standalone services (such as REST or RPC) that can be queried in a live production environment. The library also provides an object oriented client API for executing commands on the server from your applications over the wire.

---
### Servers
Server objects are standalone server implementations built on top of [React PHP](https://reactphp.org/), an event-driven concurrency framework that makes it possible to serve thousands of requests at once.

> **Note**: The server will stay running until the process is terminated. It is a good practice to use a process monitor such as [Supervisor](http://supervisord.org/) to start and autorestart the server in case of a failure.

To boot up a server, pass a trained estimator to the `serve()` method:
```php
public function serve(Estimator $estimator) : void
```

**Example**

```php
use Rubix\Server\RESTServer;
use Rubix\ML\Classifiers\KNearestNeighbors;

$server = new RESTServer('127.0.0.1', 8080);

$estimator = new KNearestNeighbors(3);

// Import dataset

$estimator->train($dataset);

$server->serve($estimator);
```

### REST Server
A standalone JSON over HTTP and secure HTTP server exposing a [REST](https://en.wikipedia.org/wiki/Representational_state_transfer) (Representational State Transfer) API. 

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | host | '127.0.0.1' | string | The host address to bind the server to. |
| 2 | port | 8888 | int | The network port to run the HTTP services on. |
| 3 | cert | None | string | The path to the certificate used to authenticate and encrypt the HTTP channel. |
| 4 | middleware | None | array | The HTTP middleware stack to run on each request. |

**HTTP Routes:**

| Method | URI | JSON Params | Description |
|--|--|--|--|
| POST | /model/predictions | `samples` | Return the predictions given by the model. |
| POST | /model/sample_prediction | `sample` | Make a prediction on a single sample. |
| POST | /model/probabilities | `samples` | Predict the probabilities of each outcome. |
| POST | /model/sample_probabilities | `sample` | Return the probabilities of a single sample. |
| POST | /model/scores | `samples` | Assign an anomaly score to each sample. |
| POST | /model/sample_score | `sample` | Assign an anomaly score to a single sample. |

**Example**

```php
use Rubix\Server\RESTServer;
use Rubix\Server\Http\Middleware\BasicAuthenticator;

$server = new RESTServer('127.0.0.1', 4443, '/cert.pem', [
    new BasicAuthenticator([
		'morgan' => 'secret',
		'taylor' => 'secret',
	]),
]);
```

### RPC Server
A lightweight [Remote Procedure Call](https://en.wikipedia.org/wiki/Remote_procedure_call) (RPC) server over HTTP and HTTPS that responds to serialized messages called [Commands](#commands).

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | host | '127.0.0.1' | string | The host address to bind the server to. |
| 2 | port | 8888 | int | The network port to run the HTTP services on. |
| 3 | cert | None | string | The path to the certificate used to authenticate and encrypt the HTTP channel. |
| 4 | middleware | | array | The HTTP middleware stack to run on each request. |
| 5 | serializer | JSON | object | The message serializer. |

**Example**

```php
use Rubix\Server\RPCServer;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use Rubix\Server\Serializers\JSON;

$server = new RPCServer('127.0.0.1', 4443, '/cert.pem', [
    new SharedTokenAuthenticator([
		'secret', 'another-key',
	]),
], new JSON());
```

---
### Clients
Clients allow you to communicate with a server over the wire using a user friendly object-oriented interface. Each client is capable of sending *commands* to the backend server with the `send()` method while handling all of the networking under the hood.

To send a Command and return a Response object:
```php
public send(Command $command) : Response
```

**Example:**

```php
use Rubix\Server\RPCClient;
use Rubix\Server\Commands\Predict;
use Rubix\ML\Datasets\Unlabeled;

$client = new RPCClient('127.0.0.1', 8888);
```

**Output:**

```sh
array(3) {
	[0]=>string(3) "red"
    [1]=>string(4) "blue"
    [2]=>string(5) "green"
}
```

### RPC Client
The RPC Client allows you to communicate with a [RPC Server](#rpc-server) over HTTP or Secure HTTP (HTTPS). In the event of a network failure, it uses a backoff and retry mechanism as a failover strategy.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | host | '127.0.0.1' | string | The address of the server. |
| 2 | port | 8888 | int | The network port that the HTTP server is running on. |
| 3 | secure | false | bool | Should we use an encrypted HTTP channel (HTTPS)?. |
| 4 | headers | Auto | array | The HTTP headers to send along with each request. |
| 5 | serializer | JSON | object | The message serializer. |
| 6 | timeout | INF | float | The number of seconds to wait before retrying. |
| 7 | retries | 2 | int | The number of retries before giving up. |
| 8 | delay | 0.3 | float | The delay between retries in seconds. |

**Example:**

```php
use Rubix\Server\RPCClient;
use Rubix\Server\Serializers\JSON;

$client = new RPCClient('127.0.0.1', 8888, false, [
    'Authorization' => 'Bearer secret',
], new JSON(), 2.5, 3, 0.5);
```

---
### HTTP Middleware
HTTP middleware are objects that process incoming HTTP requests before they are handled by a controller.

### Access Log
Generates an HTTP access log similar to the Apache log format.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | logger | | LoggerInterface | A PSR-3 logger instance. |

**Example:**

```php
use Rubix\Server\Http\Middleware\AccessLog;
use Rubix\ML\Other\Loggers\Screen;

$middleware = new AccessLog(new Screen());
```

### Basic Authenticator
An implementation of HTTP Basic Auth as described in RFC7617.

> **Note**: This strategy is only secure over an encrypted channel such as HTTPS with SSL or TLS.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | passwords | | array | An associative map from usernames to their passwords. |
| 2 | realm | 'auth' | string | The unique name given to the scope of permissions required for this server. |

**Example:**

```php
use Rubix\Server\Http\Middleware\BasicAuthenticator;

$middleware = new BasicAuthenticator([
	'morgan' => 'secret',
	'taylor' => 'secret',
], 'machine learning');
```

### Response Time
This middleware adds a response time header to every response. Response time is measured from the time the request is received by the middleware until the response is sent to the client.

**Parameters:**

This middleware does not have any parameters.

**Example:**

```php
use Rubix\Server\Http\Middleware\ResponseTime;

$middleware = new ResponseTime();
```

### Shared Token Authenticator
Authenticates incoming requests using a shared key that is kept secret between the client and server. It uses the `Authorization` header field to hold the key string.

> **Note**: This strategy is only secure over an encrypted channel such as HTTPS with SSL or TLS.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | tokens | | array | The shared secret keys (bearer tokens) used to authorize requests. |
| 2 | realm | 'auth' | string | The unique name given to the scope of permissions required for this server. |

**Example:**

```php
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;

$middleware = new SharedTokenAuthenticator([
	'secret', 'another-key',
], 'auth');
```

### Trusted Clients
A whitelist of trust clients - all other clients will be dropped.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | ips | ['127.0.0.1'] | array | An array of trusted client ip addresses. |

**Example:**

```php
use Rubix\Server\Http\Middleware\TrustedClients;

$middleware = new TrustedClients([
	'127.0.0.1', '192.168.4.1', '45.63.67.15',
]);
```

---
### Messages
Messages are containers for the data that flow across the network between clients and model servers. They provide an object oriented interface to making requests and receiving responses through client/server interaction. There are two types of messages to consider in Rubix Server - [Commands](#commands) and [Responses](#responses). Commands signal an action to be performed by the server and are instantiated by the user and sent by the client API. Responses are returned by the server and contain the data that was sent back as a result of a command.

To build a Message from an associative array:
```php
public static function fromArray() : self
```

To return the Message payload as an associative array:
```php
public function asArray() : array
```

### Commands
Commands are messages sent by clients and used internally by servers to transport data over the wire and direct the server to execute a remote procedure. They should contain all the data needed by the server to execute the request. The result of a command is a [Response](#responses) object that contains the data sent back from the server.

### Predict
Return the predictions of the samples provided in a dataset from the model running on the server.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | dataset | | Dataset | The dataset that contains the samples to predict. |

**Additional Methods:**

Return the dataset that contains the unknown samples:
```php
public dataset() : Dataset
```

**Example:**

```php
use Rubix\Server\Commands\Predict;
use Rubix\ML\Datasets\Unlabeled;

// Import samples

$command = new Predict(new Unlabeled($samples));
```

### Predict Sample
Predict a single sample and return the result.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | sample | | array | The sample to predict. |

**Additional Methods:**

Return the sample to be predicted:
```php
public sample() : array
```

**Example:**

```php
use Rubix\Server\Commands\PredictSample;

// Import sample

$command = new PredictSample($sample);
```

### Proba
Return the probabilistic predictions from a probabilistic model.

**Parameters:**
| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | dataset | | Dataset |  The dataset that contains the samples to predict. |

**Additional Methods:**

Return the dataset that contains the unknown samples:
```php
public dataset() : Dataset
```

**Example:**
```php
use Rubix\Server\Commands\Proba;
use Rubix\ML\Datasets\Unlabeled;

// Import samples

$command = new Proba(new Unlabeled($samples));
```

### Proba Sample
Predict the joint probabilities of a single sample.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | sample | | array | The sample to predict. |

**Additional Methods:**

Return the sample to be predicted:
```php
public sample() : array
```

**Example:**

```php
use Rubix\Server\Commands\ProbaSample;

// Import sample

$command = new ProbaSample($sample);
```

### Score
Score the unknown samples in a dataset in terms of their anomaly score.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | dataset | | Dataset |  The dataset that contains the samples to predict. |

**Additional Methods:**

Return the dataset that contains the unknown samples:
```php
public dataset() : Dataset
```

**Example:**

```php
use Rubix\Server\Commands\Score;
use Rubix\ML\Datasets\Unlabeled;

// Import samples

$command = new Score(new Unlabeled($samples));
```

### Score Sample
Return the anomaly score of a single sample.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | sample | | array | The sample to be scored. |

**Additional Methods:**

Return the sample to be scored:
```php
public sample() : array
```

**Example:**

```php
use Rubix\Server\Commands\ScoreSample;

// Import sample

$command = new ScoreSample($sample);
```

### Responses
Response objects are returned as a result of a [Command](#commands). They contain the data being sent back from the server.

### Error Response
This is the response from the server when something went wrong in attempting to fulfill the request. It contains an error message that describes what went wrong.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | message | | string | The error message. |

**Additional Methods:**

Return the error message from the server:
```php
public message() : string
```

**Example:**

```php
use Rubix\Server\Responses\ErrorResponse;

$response = new ErrorResponse("He's toast - but he's in a butter place now.");
```

### Predict Response
This is the response returned from a predict command containing the predictions returned from the model.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | predictions | | array | The predictions returned from the model. |

**Additional Methods:**

Return the predictions obtained from the model:
```php
public predictions() : array
```

**Example:**

```php
use Rubix\Server\Responses\PredictResponse;

// Obtain predictions from model

$response = new PredictResponse($predictions);
```

### Predict Sample Response
This is the response returned from a predict sample command containing the prediction returned from the model.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | prediction | | mixed | The prediction returned from the model. |

**Additional Methods:**

Return the prediction obtained from the model:s
```php
public prediction() : mixed
```

**Example:**

```php
use Rubix\Server\Responses\PredictSampleResponse;

// Obtain prediction from model

$response = new PredictSampleResponse($prediction);
```

### Proba Response
This is the response from a Proba command containing the probabilities obtained from the model.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | probabilities | | array | The probabilities returned from the model. |

**Additional Methods:**

Return the probabilities obtained from the model:
```php
public probabilities() : array
```

**Example:**

```php
use Rubix\Server\Responses\ProbaResponse;

// Obtain probabilities from model

$response = new ProbaResponse($probabilities);
```

### Proba Sample Response
This is the response returned from a proba sample command containing the probabilities returned from the model.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | probabilities | | array | The probabilities returned from the model. |

**Additional Methods:**

Return the probabilities obtained from the model:
```php
public probabilities() : array
```

**Example:**

```php
use Rubix\Server\Responses\ProbaSampleResponse;

// Obtain probabilities from model

$response = new ProbaSampleResponse($probabilities);
```

### Score Response
Return the anaomaly scores from a Score command.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | scores | | array | The probabilities returned from the model. |

**Additional Methods:**

Return the anomaly scores obtained from the model:
```php
public scores() : array
```

**Example:**

```php
use Rubix\Server\Responses\ScoreResponse;

// Obtain anomaly scores from model

$response = new ProbaResponse($scores);
```

### ScoreSample Response
This is the response returned from a rank sample command containing the score returned from the model.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | score | | mixed | The score returned from the model. |

**Additional Methods:**

Return the score obtained from the model:
```php
public score() : mixed
```

**Example:**

```php
use Rubix\Server\Responses\ScoreSampleResponse;

// Obtain score from model

$response = new ScoreSampleResponse($score);
```

## Testing
Rubix utilizes a combination of static analysis and unit tests for quality assurance and to reduce the number of bugs. Rubix provides three [Composer](https://getcomposer.org/) scripts that can be run from the root directory to automate the testing process.

To run static analysis:
```sh
$ composer analyze
```

To run the style checker:
```sh
$ composer check
```

To run the unit tests:
```sh
$ composer test
```

## License
The code is licensed [MIT](LICENSE.md) and the documentation is licensed [CC BY-NC 4.0](https://creativecommons.org/licenses/by-nc/4.0/).