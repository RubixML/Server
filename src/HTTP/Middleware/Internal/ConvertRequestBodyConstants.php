<?php

namespace Rubix\Server\HTTP\Middleware\Internal;

use Psr\Http\Message\ServerRequestInterface;

class ConvertRequestBodyConstants
{
    /**
     * @var array<string,float>
     */
    protected const REPLACEMENTS = [
        'INF' => INF,
        'NAN' => NAN,
    ];

    /**
     * @param mixed[] $sample
     */
    protected function convertConstants(array &$sample) : void
    {
        $replace = function (&$value) {
            if (is_string($value)) {
                if (isset(self::REPLACEMENTS[$value])) {
                    $value = self::REPLACEMENTS[$value];
                }
            }
        };

        array_walk($sample, $replace);
    }

    /**
     * Converts INF and NAN string sample values (if any) into respective PHP constants.
     *
     * @internal
     *
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return \React\Promise\PromiseInterface
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $body = $request->getParsedBody();

        if (is_array($body) && !empty($body['samples'])) {
            array_walk($body['samples'], [$this, 'convertConstants']);

            $request = $request->withParsedBody($body);
        }

        return $next($request);
    }
}
