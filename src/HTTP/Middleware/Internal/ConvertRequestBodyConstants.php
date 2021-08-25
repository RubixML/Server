<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Psr\Http\Message\ServerRequestInterface;

class ConvertRequestBodyConstants
{
    /**
     * @var array<string, float>
     */
    private const REPLACEMENTS = [
        'INF' => INF,
        'NAN' => NAN,
    ];

    /**
     * @param mixed[] $body
     * @return mixed[]
     */
    private function convert(array $body) : array
    {
        $body['samples'] = array_map([$this, 'normalizeSample'], $body['samples']);

        return $body;
    }

    /**
     * @param mixed[] $sample
     * @return mixed[]
     */
    private function normalizeSample(array $sample) : array
    {
        return array_map(function ($value) {
            return self::REPLACEMENTS[$value] ?? $value;
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

        if (is_array($body) && !empty($body['samples'])) {
            $request = $request->withParsedBody($this->convert($body));
        }

        return $next($request);
    }
}
