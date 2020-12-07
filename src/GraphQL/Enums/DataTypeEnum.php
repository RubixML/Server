<?php

namespace Rubix\Server\GraphQL\Enums;

use GraphQL\Type\Definition\Type;

class DataTypeEnum extends EnumType
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
            'name' => 'DataType',
            'description' => 'A high-level data type.',
            'values' => [
                'CONTINUOUS' => [
                    'value' => 1,
                ],
                'CATEGORICAL' => [
                    'value' => 2,
                ],
                'IMAGE' => [
                    'value' => 3,
                ],
                'OTHER' => [
                    'value' => 4,
                ],
            ],
        ]);
    }
}
