<?php

namespace Sys\framework;

/**
 * Logger Class
 *
 * This class provides logging functionality to write logs to a file.
 *
 * @category  Utility
 * @package   Logging
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   1.0.0
 */
class Logger
{
    /**
     * @var string The path
     */
    private $path = '../../';

    /**
     * @var string The directory path where logs files will be stored.
     */
    private $logFilePath = '../../storage/logs/debug.log';

    /**
     * Constructor to initialize the log file path.
     *
     * @param string $logFilePath The path to the log file.
     */
    public function __construct()
    {
        $this->createLogFileIfNotExists();
    }

    /**
     * Create the log file if it doesn't exist with permissions to write.
     *
     * @return void
     */
    private function createLogFileIfNotExists()
    {
        if (!file_exists($this->logFilePath)) {
            $directory = dirname($this->logFilePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0775, true);
            }
            touch($this->logFilePath);
            chmod($this->logFilePath, 0666);
        }
    }

    /**
     * Logs a message to the log file with the specified type.
     *
     * @param string $message The message to be logged.
     * @param string $type    The type of log message (DEBUG, ERROR, INFO).
     * @return void
     */
    public function log($message, $type = NULL)
    {
        $formattedMessage = '[' . date('Y-m-d h:i:s A') . ']';
        if (!is_null($type)) {
            $formattedMessage .= ' [' . strtoupper($type) . ']';
        }
        $formattedMessage .= ' - ' . $message . PHP_EOL;

        $this->createLogFileIfNotExists();

        file_put_contents($this->logFilePath, $formattedMessage, FILE_APPEND);
    }

    /**
     * Logs a debug message.
     *
     * @param string $message     The debug message to be logged.
     * @param string $logFilePath The path to the debug log file. Defaults to 'storage/logs/debug.log'.
     * @return void
     */
    public function debug($message, $logFilePath = 'storage/logs/debug.log')
    {
        $this->logFilePath = $this->path . $logFilePath;
        $this->log($message, 'debug');
    }

    /**
     * Logs an error message.
     *
     * @param string $message     The error message to be logged.
     * @param string $logFilePath The path to the error log file. Defaults to 'storage/logs/error.log'.
     * @return void
     */
    public function error($message, $logFilePath = 'storage/logs/error.log')
    {
        $this->logFilePath = $this->path . $logFilePath;
        $this->log($message, 'error');
    }

    /**
     * Logs an info message.
     *
     * @param string $message     The info message to be logged.
     * @param string $logFilePath The path to the info log file. Defaults to 'storage/logs/info.log'.
     * @return void
     */
    public function info($message, $logFilePath = 'storage/logs/info.log')
    {
        $this->logFilePath = $this->path . $logFilePath;
        $this->log($message, 'info');
    }

    /**
     * Logs a warning message.
     *
     * @param string $message     The warning message to be logged.
     * @param string $logFilePath The path to the warning log file. Defaults to 'storage/logs/warning.log'.
     * @return void
     */
    public function warning($message, $logFilePath = 'storage/logs/warning.log')
    {
        $this->logFilePath = $this->path . $logFilePath;
        $this->log($message, 'warning');
    }
}
