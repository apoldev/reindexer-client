<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/common-part.php';

use Reindexer\Services\Item;
use Reindexer\Services\Query;

try {
    $users = [
        [
            'id' => 1, 'name' => 'John Doe'
        ],
        [
            'id' => 2, 'name' => 'Tom Soyer'
        ],
        [
            'id' => 3, 'name' => 'James Bond'
        ]
    ];
    $configData = file_get_contents(__DIR__ . '/config.json');
    $config = json_decode($configData, true);
    $configuration = new Configuration($config);

    $api = $configuration->getApi();
    $databaseName = $argv[1] ?? 'test';
    $namespaceName = $argv[2] ?? 'namespace';

    $itemService = new Item($api);
    $sqlService = new Query($api);
    $itemService->setNamespace($namespaceName);
    $itemService->setDatabase($databaseName);
    $sqlService->setDatabase($databaseName);

    foreach ($users as $user) {
        $response = $itemService->add($user);
    }

    $response = $sqlService->createByHttpGet("SELECT * from $namespaceName");
    $response = $sqlService->createSqlQueryByHttpPost("UPDATE $namespaceName SET name = 'John Doe changed 2' WHERE id = 1");
    var_dump($response->getResponseBody());
} catch (\Throwable $e) {
    echo sprintf(
        'Error %s in file %s on line %s',
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );
}
