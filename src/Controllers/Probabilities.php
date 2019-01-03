<?php

namespace Rubix\Server\Controllers;

use Rubix\ML\Probabilistic;
use Rubix\ML\Datasets\Unlabeled;
use React\Http\Response as ReactResponse;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Exception;

class Probabilities extends Controller
{
    const HEADERS = [
        'Content-Type' => 'text/json',
    ];

    /**
     * The probabilistic estimator instance.
     * 
     * @var \Rubix\ML\Probabilistic
     */
    protected $estimator;

    /**
     * @param  \Rubix\ML\Probabilistic $estimator
     * @return void
     */
    public function __construct(Probabilistic $estimator)
    {
        $this->estimator = $estimator;
    }

    /**
     * Handle the request.
     * 
     * @param  Request  $request
     * @param  array  $params
     * @return Response
     */
    public function handle(Request $request, array $params) : Response
    {
        $json = json_decode($request->getBody()->getContents(), true);

        if (!isset($json['sample'])) {
            return new ReactResponse(400, self::HEADERS, json_encode([
                'error' => 'Missing sample field in request body.',
            ]));
        }

        try {
            $dataset = Unlabeled::build($json['sample']);
            
            $probabilities = $this->estimator->proba($dataset);
        } catch (Exception $e) {
            return new ReactResponse(500, self::HEADERS, json_encode([
                'error' => $e->getMessage(),
            ]));
        }

        return new ReactResponse(200, self::HEADERS, json_encode([
            'probabilities' => $probabilities[0],
        ]));
    }
}