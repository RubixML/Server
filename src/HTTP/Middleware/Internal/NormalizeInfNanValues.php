<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Psr\Http\Message\ServerRequestInterface;

class NormalizeInfNanValues
{
    private array $replacements = [
        'INF' => INF,
        'NAN' => NAN,
    ];

    private function normalize(array $body) : array
    {
        $body['samples'] = array_map([$this, 'normalizeSample'], $body['samples']);

        return $body;
    }

    private function normalizeSample(array $sample) : array
    {
        return array_map(function ($value) {
            return $this->replacements[$value] ?? $value;
        }, $sample);
    }

    /**
     * Converts INF and NAN string sample values (if any) into respective PHP constants.
     *
     * @internal
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param callable $next
     * @return \React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $body = $request->getParsedBody();

        if (!empty($body['samples'])) {
            $request = $request->withParsedBody($this->normalize($body));
        }

        return $next($request);
    }
}
