<?php

namespace Rubix\Server\GraphQL\InputObjects;

use Rubix\Server\GraphQL\Scalars\FeatureScalar;
use GraphQL\Type\Definition\Type;

class DatasetInputObject extends InputObjectType
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
            'name' => 'Dataset',
            'description' => 'A dataset object.',
            'fields' => [
                'samples' => [
                    'description' => 'The samples of the dataset.',
                    'type' => Type::nonNull(Type::listOf(Type::listOf(FeatureScalar::singleton()))),
                ],
            ],
        ]);
    }
}
