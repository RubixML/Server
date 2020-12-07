<?php

namespace Rubix\Server\GraphQL\Enums;

use GraphQL\Type\Definition\Type;

class EstimatorTypeEnum extends EnumType
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
            'name' => 'EstimatorType',
            'description' => 'The estimator type.',
            'values' => [
                'CLASSIFIER' => [
                    'value' => 1,
                ],
                'REGRESSOR' => [
                    'value' => 2,
                ],
                'CLUSTERER' => [
                    'value' => 3,
                ],
                'ANOMALY_DETECTOR' => [
                    'value' => 4,
                ],
            ],
        ]);
    }
}
