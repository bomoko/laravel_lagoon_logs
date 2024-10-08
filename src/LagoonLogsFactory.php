<?php

namespace amazeeio\LagoonLogs;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\FallbackGroupHandler;
use Monolog\Handler\SocketHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\LogRecord;

/**
 * Class LagoonLoggerFactory
 *
 * @package amazeeio\LagoonLogs
 */
class LagoonLogsFactory
{

    /**
     * Default chunk size in bytes.
     *
     * @var int
     */
    const LAGOON_LOGS_DEFAULT_CHUNK_SIZE_BYTES = 15000;

    /**
     * Default identifier.
     *
     * @var string
     */
    const LAGOON_LOGS_MONOLOG_CHANNEL_NAME = 'LagoonLogs';

    /**
     * Default identifier.
     *
     * @var string
     */
    const LAGOON_LOGS_DEFAULT_IDENTIFIER = 'laravel';

    /**
     * Default name.
     *
     * @var string
     */
    const LAGOON_LOGS_DEFAULT_HOSTNAME = "application-logs.lagoon.svc";

    /**
     * Default host port.
     *
     * @var int
     */
    const LAGOON_LOGS_DEFAULT_HOSTPORT = 5140;

    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {

        $logger = new Logger(self::LAGOON_LOGS_MONOLOG_CHANNEL_NAME);

        $connectionString = sprintf(
            "udp://%s:%s",
            $config['host'] ?? self::LAGOON_LOGS_DEFAULT_HOSTNAME,
            $config['port'] ?? self::LAGOON_LOGS_DEFAULT_HOSTPORT
        );
        $udpHandler = new SocketHandler($connectionString);
        $udpHandler->setChunkSize(self::LAGOON_LOGS_DEFAULT_CHUNK_SIZE_BYTES);
        $udpHandler->setFormatter(
            new LagoonLogsFormatter(
                $config['identifier'] ?? self::getDefaultIdentifier()
            )
        );

        // We want to wrap the group in a failure handler so that if
        // the logstash instance isn't available, it pushes to std
        // which will be available via the docker logs
        $fallbackHandler = new StreamHandler('php://stdout');

        $failureGroupHandler = new FallbackGroupHandler([
            $udpHandler,
            $fallbackHandler,
        ]);

        $logger->pushHandler($failureGroupHandler);
        $logger->pushProcessor([$this, 'addExtraFields']);

        return $logger;
    }

    /**
     * Add extra fields to the log record.
     */
    public function addExtraFields(array $record): array
    {
        $record['extra']['application'] = self::LAGOON_LOGS_DEFAULT_IDENTIFIER;
        $record['extra']['environment'] = $_SERVER['LAGOON_ENVIRONMENT'] ?? '';
        $record['extra']['project'] = $_SERVER['LAGOON_PROJECT'] ?? '';
        return $record;
    }

    /**
     * Interrogates environment to get the correct process index for logging
     */
    public static function getDefaultIdentifier(): string
    {

        $defaultIdentifier = self::LAGOON_LOGS_DEFAULT_IDENTIFIER;

        // Attempt to parse Lagoon instance details as a better alternative.
        if (getenv('LAGOON_PROJECT') && getenv('LAGOON_GIT_SAFE_BRANCH')) {
            $defaultIdentifier = implode('-', [
                getenv('LAGOON_PROJECT'),
                getenv('LAGOON_GIT_SAFE_BRANCH'),
            ]);
        }

        return $defaultIdentifier;
    }
}
