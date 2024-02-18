<?php

namespace Rubix\Server\GraphQL\Scalars;

use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Error\Error;

use function is_int;

class LongIntegerScalar extends ScalarType
{
    /**
     * The singleton instance of the object type.
     *
     * @var self|null
     */
    protected static $instance;

    /**
     * @return self
     */
    public static function singleton() : self
    {
        return self::$instance ?? self::$instance = new self([
            'name' => 'Long Integer',
            'description' => 'A long integer.',
        ]);
    }

    /**
     * Serializes an internal value to include in a response.
     *
     * @param int $value
     * @return int
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @throws Error
     * @return int
     */
    public function parseValue($value)
    {
        if (!is_int($value)) {
            throw new Error('Value must be an integer.');
        }

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * @param Node $node
     * @param mixed[]|null $variables
     * @return mixed
     */
    public function parseLiteral(Node $node, ?array $variables = null)
    {
        if (!$node instanceof IntValueNode) {
            throw new Error('Value must be an integer.');
        }

        return $node->value;
    }
}
