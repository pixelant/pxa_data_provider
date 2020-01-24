<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Configurable Data Provider',
    'description' => 'Configurable data provider for easy data sharing with external scripts such as Google Tag Manager.',
    'category' => 'plugin',
    'author' => 'Pixelant',
    'author_email' => 'info@pixelant.net',
    'author_company' => 'Pixelant',
    'state' => 'alpha',
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'version' => '0.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ]
];
