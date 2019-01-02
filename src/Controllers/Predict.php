<?php

namespace Rubix\Server\Controllers;

use Rubix\ML\Estimator;
use Rubix\ML\Datasets\Unlabeled;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use React\Http\Response;
use Exception;

class Predict implements Controller
{
    /**
     * The estimator instance.
     * 
     * @var \Rubix\ML\Estimator
     */
    protected $estimator;

    /**
     * @param  \Rubix\ML\Estimator  $estimator
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

        $dataset = Unlabeled::build($json['sample'] ?? []);

        try {
            $predictions = $this->estimator->predict($dataset);
        } catch (Exception $e) {
            return new Response(500, self::HEADERS, [
                'error' => $e->getMessage(),
            ]);
        }

        return new Response(200, self::HEADERS, json_encode([
            'prediction' => $predictions[0],
        ]));
    }
}