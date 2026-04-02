<?php
return [
    'statuses' => [
        'pending' => [
            'hours' => 5,
            'next' => 'processing'
        ],
        'processing' => [
            'days' => 2,
            'next' => 'dispatched'
        ],
        'dispatched' => [
            'days' => 1,
            'next' => 'delivered'
        ],
    ],
];