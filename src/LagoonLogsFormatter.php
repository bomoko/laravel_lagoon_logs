<?php

namespace amazeeio\LagoonLogs;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Formatter\NormalizerFormatter;
use Monolog\Logger;
use Monolog\LogRecord;

/**
 * Lagoon Logs Formatter.
 */
class LagoonLogsFormatter extends LogstashFormatter
{

    /**
     * {@inheritDoc}
     */
    public function format(array $record): string
    {
        $normalized = $this->normalize($record);

        $message = [
            '@timestamp' => $normalized['datetime'],
            '@version' => 1,
            'host' => $this->systemName,
        ];

        if (isset($normalized['message'])) {
            $message['message'] = $normalized['message'];
        }
        if (isset($normalized['channel'])) {
            $message['type'] = $normalized['channel'];
            $message['channel'] = $normalized['channel'];
        }
        if (isset($normalized['level_name'])) {
            $message['level'] = $normalized['level_name'];
        }
        if (isset($normalized['level'])) {
            $message['monolog_level'] = $normalized['level'];
        }
        if ($this->applicationName) {
            $message['type'] = $this->applicationName;
        }

        if (isset($normalized['extra'])) {
            foreach ($normalized['extra'] as $key => $val) {
                $message[$key] = $val;
            }
        }

        if (!empty($record['context'])) {
            foreach ($record['context'] as $key => $val) {
                $message[$key] = $val;
            }
        }

        return $this->toJson($message) . "\n";
    }
}
