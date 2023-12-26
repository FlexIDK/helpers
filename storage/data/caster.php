<?php
use One23\Helpers;
use One23\Helpers\Enums;

return [
    'id' => [
        'type' => Enums\CasterType::Int,
        'filter' => Enums\CasterFilter::Gt0,
    ],

    'type_ids' => [
        'type' => Enums\CasterType::ArrOfInt,
        'filter' => [
            Enums\CasterFilter::Gte0,
            Enums\CasterFilter::ArrNotNull,
            Enums\CasterFilter::ArrUniqueInt,
            Enums\CasterFilter::ArrValues,
        ],
    ],

    'tags' => [
        'type' => Enums\CasterType::ArrOfStr,
        'filter' => [
            Enums\CasterFilter::ArrUniqueStr,
            Enums\CasterFilter::ArrNotNull,
            Enums\CasterFilter::ArrValues,
        ],
    ],

    'user_ids' => [
        'type' => Enums\CasterType::ArrOfInt,
        'filter' => [
            Enums\CasterFilter::Gt0,
            Enums\CasterFilter::ArrUniqueInt,
            Enums\CasterFilter::ArrValues,
        ],
    ],

    'last_ids' => [
        'type' => Enums\CasterType::ArrOfInt,
        'filter' => [
            Enums\CasterFilter::Gt0,
            Enums\CasterFilter::ArrNotNull
        ],
    ],

    'incoming' => [
        'type' => Enums\CasterType::ArrOfFloat,
        'filter' => Enums\CasterFilter::Gte0,
    ],

    'active' => Enums\CasterType::Boolean,

    'bool1' => Enums\CasterType::Boolean,
    'bool2' => Enums\CasterType::Boolean,
    'bool3' => Enums\CasterType::Boolean,
    'bool4' => Enums\CasterType::Boolean,
    'bool5' => Enums\CasterType::Boolean,

    'name' => [
        'type' => Enums\CasterType::Str,
    ],

    'rating' => Enums\CasterType::Float,

    'created_at' => [
        'type' => Enums\CasterType::Str,
        'filter' => function(mixed $val): mixed {
            return Helpers\Datetime::val($val)?->toDateTimeString();
        },
    ],

    'timestamp' => [
        'type' => Enums\CasterType::Datetime,
    ],

    'updated_at' => [
        'type' => Enums\CasterType::Datetime,
    ],

    'birthday' => [
        'type' => Enums\CasterType::Date,
    ],

    'deleted_at' => [
        'type' => Enums\CasterType::Carbon,
    ],
];