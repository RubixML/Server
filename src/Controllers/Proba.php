<?php

namespace Rubix\Server\Controllers;

use Rubix\ML\Estimator;
use Rubix\ML\Probabilistic;
use Rubix\ML\Wrapper;
use Rubix\ML\Datasets\Unlabeled;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use React\Http\Response;
use RuntimeException;
use Exception;

class Proba implements Controller
{
    /**
     * The probabilistic estimator instance.
     * 
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * @param  \Rubix\ML\Estimator $estimator
     * @return void
     */
    public function __construct(Estimator $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the request.
     * 
     * @param  Request  $request
     * @param  array  $params
     * @return ResponseInterface
     */
    public function handle(Request $request, array $params) : ResponseInterface
    {
        $json = json_decode($request->getBody()->getContents(), true);

        if (!isset($json['sample'])) {
            return new Response(400, self::HEADERS, [
                'error' => 'Missing sample field in request body.',
            ]);
        }

        $dataset = Unlabeled::build($json['sample']);

        try {
            if ($this->estimator instanceof Probabilistic) {
                $probabilities = $this->estimator->proba($dataset);
            } else {
                throw new RuntimeException('A probabilistic'
                    . ' estimator is needed to handle this'
                    . ' request.');
            }
        } catch (Exception $e) {
            return new Response(500, self::HEADERS, [
                'error' => $e->getMessage(),
            ]);
        }

        return new Response(200, self::HEADERS, json_encode([
            'probabilities' => $probabilities[0],
        ]));
    }
}