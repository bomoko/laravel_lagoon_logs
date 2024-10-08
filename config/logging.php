<?php

return [
    'driver' => 'custom',
    'via' => 'amazeeio\LagoonLogs\LagoonLogsFactory',
    'level' => env('LOG_LEVEL', 'debug'),
    'host' => env('LAGOON_LOGS_HOST', 'application-logs.lagoon.svc'),
    'port' => env('LAGOON_LOGS_PORT', 5140),
    'identifier' => env('LAGOON_LOGS_IDENTIFIER'),
];
