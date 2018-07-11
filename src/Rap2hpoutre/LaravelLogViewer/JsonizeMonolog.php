<?php

namespace Rap2hpoutre\LaravelLogViewer;

use Monolog\Formatter\JsonFormatter;

class JsonizeMonolog
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new JsonFormatter);
        }
        $logger->pushProcessor(function ($record) {
            if (isset($record['context']) && isset($record['context']['exception'])) {
                $e = $record['context']['exception'];
                $record['context']['exception'] = [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'class' => get_class($e),
                    // TODO $e->getPrevious()
                ];
                $record['message'] .= ' | ' . get_class($e) .
                    ": {$e->getMessage()} (code: {$e->getCode()}) at {$e->getFile()} ({$e->getLine()})";
            }
            return $record;
        });
    }
}
