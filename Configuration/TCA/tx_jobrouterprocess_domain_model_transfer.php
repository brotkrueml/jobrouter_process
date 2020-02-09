<?php
return [
    'ctrl' => [
        'title' => 'Transfer',
        'label' => 'identifier',
        'crdate' => 'crdate',
        'rootLevel' => 1,
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'instance_uid, identifier, transmit_success, transmit_date, transmit_message',
    ],
    'columns' => [
        'instance_uid' => [
            'label' => 'Table',
            'config' => [
                'type' => 'input',
            ],
        ],
        'identifier' => [
            'label' => 'Identifier',
            'config' => [
                'type' => 'input',
            ],
        ],
        'data' => [
            'label' => 'Data',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_success' => [
            'label' => 'Transmit success',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_date' => [
            'label' => 'Transmit date',
            'config' => [
                'type' => 'input',
            ],
        ],
        'transmit_message' => [
            'label' => 'Transmit message',
            'config' => [
                'type' => 'input',
            ],
        ],
    ],
    'types' => [
        '1' => ['showitem' => 'instance_uid, identifier, data, transmit_success, transmit_date, transmit_message'],
    ],
];
