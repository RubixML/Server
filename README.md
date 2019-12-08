# Rubix Model Server
A library to serve your [Rubix ML](https://github.com/RubixML/RubixML) models in production quickly and effortlessly.

## Installation
Install Rubix Server using [Composer](https://getcomposer.org/):

```sh
$ composer require rubix/server
```

## Requirements
- [PHP](https://php.net/manual/en/install.php) 7.2 or above

#### Optional
- [Igbinary extension](https://github.com/igbinary/igbinary) for binary messaging

## Documentation

### Table of Contents
- [Getting Started](#getting-started)
- [Servers](#servers)
	- [REST Server](#rest-server)
	- [RPC Server](#rpc-server)
- [Clients](#clients)
	- [RPC Client](#rpc-client)
- [Http Middleware](#http-middeware)
	- [Shared Token Authenticator](#shared-token-authenticator)
- [Messages](#messages)
	- [Commands](#commands)
		- [Predict](#predict)
		- [Predict Sample](#predict-sample)
		- [Proba](#proba)
		- [Proba Sample](#proba-sample)
		- [Query Model](#query-model)
		- [Rank](#rank)
		- [Rank Sample](#rank-sample)
		- [Server Status](#server-status)
	- [Responses](#responses)
        - [Error Response](#error-response)
        - [Predict Response](#predict-response)
		- [Predict Sample Response](#predict-sample-response)
        - [Proba Response](#proba-response)
		- [Proba Sample Response](#proba-sample-response)
        - [Query Model Response](#query-model-response)
        - [Rank Response](#rank-response)
		- [Rank Sample Response](#rank-sample-response)
        - [Server Status Response](#server-status-response)

---
### Getting Started
Once you've trained a learner in Rubix ML, the next step is to use it to make predictions. If the model is going to used to make predictions in real-time (as opposed to offline) then you'll need to make it availble to clients through a *server*. Rubix ML model servers expose your estimators as standalone services (such as REST or RPC) that can be queried in a live production environment. The library also provides an object oriented client API for executing commands on the server from your applications.

---
### Servers
Server objects are standalone server implementations built on top of [React PHP](https://reactphp.org/), an event-driven concurrency framework that makes it possible to serve thousands of requests at once.

> **Note**: The server will stay running until the process is terminated. It is a good practice to use a process monitor such as [Supervisor](http://supervisord.org/) to start and autorestart the server in case there is a failure.

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
A standalone Json over HTTP and secure HTTP server exposing a [REST](https://en.wikipedia.org/wiki/Representational_state_transfer) (Representational State Transfer) API. 

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | host | '127.0.0.1' | string | The host address to bind the server to. |
| 2 | port | 8888 | int | The network port to run the HTTP services on. |
| 3 | cert | None | string | The path to the certificate used to authenticate and encrypt the HTTP channel. |
| 4 | middleware | None | array | The HTTP middleware stack to run on each request. |

**HTTP Routes:**

| Method | URI | Json Params | Description |
|--|--|--|--|
| GET | /model | | Query information about the model. |
| POST | /model/predictions | `samples` | Return the predictions given by the model. |
| POST | /model/sample_prediction | `sample` | Make a prediction on a single sample. |
| POST | /model/probabilities | `samples` | Predict the probabilities of each outcome. |
| POST | /model/sample_probabilities | `sample` | Return the probabilities of a single sample. |
| POST | /model/scores | `samples` | Assign an anomaly score to each sample. |
| POST | /model/sample_score | `sample` | Assign an anomaly score to a single sample. |
| GET | /server/status | | Query the status of the server. |

**Example**

```php
use Rubix\Server\RESTServer;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;

$server = new RESTServer('127.0.0.1', 4443, '/cert.pem', [
    new SharedTokenAuthenticator('secret'),
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
| 5 | serializer | Json | object | The message serializer. |

**Example**

```php
use Rubix\Server\RPCServer;
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;
use Rubix\Server\Serializers\Binary;

$server = new RPCServer('127.0.0.1', 4443, '/cert.pem', [
    new SharedTokenAuthenticator('secret'),
], new Binary());
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

$response = $client->send(new Predict(new Unlabeled($samples)));

$predictions = $response->predictions();

var_dump($predictions);
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
| 5 | serializer | Json | object | The message serializer. |
| 6 | timeout | INF | float | The number of seconds to wait before retrying. |
| 7 | retries | 2 | int | The number of retries before giving up. |
| 8 | delay | 0.3 | float | The delay between retries in seconds. |

**Example:**

```php
use Rubix\Server\RPCClient;
use Rubix\Server\Serializers\Binary;

$client = new RPCClient('127.0.0.1', 8888, false, [
    'Authorization' => 'secret',
], new Binary(), 2.5, 3, 0.5);
```

---
### HTTP Middleware
HTTP middleware are objects that process incoming HTTP requests before they are handled by a controller.

### Shared Token Authenticator
Authenticates incoming requests using a shared key that is kept secret between the client and server. It uses the `Authorization` header field to hold the key string.

> **Note**: This strategy is only secure over an encrypted channel such as HTTPS with SSL or TLS.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | token | | string | The shared secret key (token) required to authenticate every request. |

**Example:**

```php
use Rubix\Server\Http\Middleware\SharedTokenAuthenticator;

$middleware = new SharedTokenAuthenticator('secret');
```

---
### Messages
Messages are containers for the data that flow accross the network between clients and model servers. They provide an object oriented interface to making requests and receiving responses through client/server interaction. There are two types of messages to consider in Rubix Server - [Commands](#commands) and [Responses](#responses). Commands signal an action to be performed by the server and are instantiated by the user and sent by the client API. Responses are returned by the server and contain the data that was sent back as a result of a command.

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

### Query Model
Query the status of the current model being served.

**Parameters:**

This command does not have any parameters.

**Additional Methods:**

This command does not have any additional methods.

**Example:**
```php
use Rubix\Server\Commands\QueryModel;

$command = new QueryModel();
```

### Rank
Rank the unknown samples in a dataset in terms of their anomaly score.

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
use Rubix\Server\Commands\Rank;
use Rubix\ML\Datasets\Unlabeled;

// Import samples

$command = new Rank(new Unlabeled($samples));
```

### Rank Sample
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
use Rubix\Server\Commands\RankSample;

// Import sample

$command = new RankSample($sample);
```

### Server Status
Return statistics regarding the server status such as uptime, requests per minute, and memory usage.

**Parameters:**

This command does not have any parameters.

**Additional Methods:**

This command does not have any additional methods.

**Example:**

```php
use Rubix\Server\Commands\ServerStatus;

$command = new ServerStatus();
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
| 1 | probabilities | | array | The probabilties returned from the model. |

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

### Query Model Response
This response contains the properties of the underlying estimator instance being served such as type and compatibility.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | type | | int | The estimator type i.e. classifier, regressor, etc. |
| 2 | compatibility | | array | The data types that the estimator is compatible with. |
| 3 | probabilistic | | bool | Is the model probabilistic? |
| 4 | ranking | | bool | Is the model a ranking estimator? |

**Additional Methods:**

Return the type of estimator:
```php
public type() : string
```

Return the data types the estimator is compatible with:
```php
public compatibility() : array
```

Is the model probabilistic?
```php
public probabilistic() : bool
```

Is the model a ranking estimator?
```php
public ranking() : bool
```

**Example:**

```php
use Rubix\Server\Responses\QueryModelResponse;
use Rubix\ML\Other\Helpers\DataType;

$response = new QueryModelResponse('classifier', [DataType::CONTINUOUS], true, false);
```

### Rank Response
Return the anaomaly scores from a Rank command.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | scores | | array | The probabilties returned from the model. |

**Additional Methods:**

Return the anomaly scores obtained from the model:
```php
public scores() : array
```

**Example:**

```php
use Rubix\Server\Responses\RankResponse;

// Obtain anomaly scores from model

$response = new ProbaResponse($scores);
```

### RankSample Response
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
use Rubix\Server\Responses\RankSampleResponse;

// Obtain score from model

$response = new RankSampleResponse($score);
```

### Server Status Response
A response containing the status of the currently running server.

**Parameters:**

| # | Param | Default | Type | Description |
|--|--|--|--|--|
| 1 | requests | | array | An associative array of request statistics. |
| 2 | memory usage | | array | An associative array of memory usage statistics. |
| 3 | uptime | | int | The number of seconds the server has been up. |

**Additional Methods:**

Return the request statistics:
```php
public requests() : array
```

Return the memory usage statistics:
```php
public memoryUsage() : array
```

Return the uptime of the server.
```php
public uptime() : int
```

**Example:**

```php
use Rubix\Server\Responses\ServerStatusResponse;

$response = new ServerStatusResponse($requests, $memoryUsage, 16);
```

---
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

---
## License
[MIT](https://github.com/RubixML/Server/blob/master/LICENSE.md)