<?php

namespace Rubix\Server\GraphQL\Scalars;

use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\FloatValueNode;
use GraphQL\Error\Error;

use function is_string;
use function is_numeric;

class FeatureScalar extends ScalarType
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
            'name' => 'Feature',
            'description' => 'A feature value of a sample.',
        ]);
    }

    /**
     * Serializes an internal value to include in a response.
     *
     * @param string|int|float $value
     * @return string|int|float
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
     * @return string|int|float
     */
    public function parseValue($value)
    {
        if (!is_string($value) and !is_numeric($value)) {
            throw new Error('Feature must be a string or numeric type.');
        }

        return $value;
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query) to use as an input.
     *
     * @param Node $node
     * @param mixed[]|null $variables
     * @return string|int|float
     */
    public function parseLiteral(Node $node, ?array $variables = null)
    {
        if (!$node instanceof StringValueNode and !$node instanceof IntValueNode and !$node instanceof FloatValueNode) {
            throw new Error('Feature must be a string or numeric type.');
        }

        return $node->value;
    }
}
