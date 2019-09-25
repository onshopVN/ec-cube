<?php
namespace Customize\Monolog;

class OsMonitorHandler extends \Monolog\Handler\AbstractHandler
{
    /**
     * OsMonitorHandler constructor.
     * @param int $level
     * @param bool $bubble
     * @throws \Exception
     */
    public function __construct(
        $level = \Monolog\Logger::WARNING,
        $bubble = true
    ) {
        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     *
     * @param array $record
     * @return bool
     */
    public function handle(array $record)
    {
        if (!$this->isHandling($record)) {
            return false;
        }

        $token = getenv('OS_STORE_AUTH_TOKEN');
        $apiEndpoint = getenv('OS_STORE_API_ENDPOINT');
        $exception = isset($record['context']['exception']) ? $record['context']['exception'] : null;
        if ($exception instanceof \Exception && $token && $apiEndpoint) {
            $header = sprintf('Authorization: Bearer %s', $token);
            $endpoint =  $apiEndpoint . '/api/v1/monitor/exception';
            $data = json_encode([
                'domain' =>  isset($record['extra']['server']) ? $record['extra']['server'] : $_SERVER['HTTP_HOST'],
                'type' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'severity' => method_exists($exception, 'getSeverity') ? $exception->getSeverity() : '',
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
                'extra' => isset($record['extra']) ? json_encode($record['extra']) : '{}'
            ]);
            $data = str_replace("'", "&#39;", $data);
            $cmd = sprintf('curl -X POST -H "Content-Type: application/json" -H "%s" -d \'%s\' %s', $header, $data, $endpoint);
            exec($cmd,$output);
        }
    }
}