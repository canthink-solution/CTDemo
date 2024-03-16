<?php

namespace Sys\framework;

/**
 * TaskRunner Class
 *
 * This class allows you to run tasks in parallel with a specified maximum number of concurrent tasks. 
 *
 * @category  Utility
 * @package   TaskRunner
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   1.0.0
 */

class TaskRunner
{
    /**
     * @var int The maximum number of concurrent tasks allowed.
     */
    private $maxConcurrentTasks = 4;

    /**
     * @var array An array of tasks to be executed.
     */
    private $tasks = [];

    /**
     * @var array An array of tasks currently be executed.
     */
    private $runningTasks = [];

    /**
     * @var string The directory path where job files will be stored.
     */
    private $jobsDir = '../../app/jobs/';

    /**
     * @var string The directory path where log files will be stored. 
     */
    private $logDir = 'storage/';

    /**
     * @var string|null The file path where task outputs will be logged, or null if logging is disabled.
     */
    private $logPath;

    /**
     * @var string PHP CLI command for current environment
     */
    private $phpCommand = 'php';

    /**
     * @var int Default process timeout for tasks in seconds.
     */
    private $processTimeout = 300; // 5 minutes

    /**
     * TaskRunner constructor.
     */
    public function __construct()
    {
    }

    /**
     * Add a task to the TaskRunner.
     *
     * @param string $command Command to execute
     * @param array|null $params Parameters for the command (optional)
     */
    public function addTask($command, $params = null)
    {
        if (!is_array($params) && $params !== null) {
            throw new \InvalidArgumentException("Parameters must be an array or null.");
        }

        $this->tasks[] = compact('command', 'params');
    }

    /**
     * Set the maximum number of concurrent tasks.
     *
     * @param int $maxConcurrentTasks Maximum number of tasks to run concurrently
     */
    public function setMaxConcurrentTasks($maxConcurrentTasks)
    {
        $this->maxConcurrentTasks = $maxConcurrentTasks;
    }

    /**
     * Set the default process timeout for tasks.
     *
     * @param int $timeout Timeout value in seconds
     */
    public function setProcessTimeout($timeout = 300)
    {
        $this->processTimeout = $timeout;
    }

    /**
     * Set the log path for the TaskRunner.
     *
     * @param string $logPath Path to the log file
     * @param int $permissions Permissions for the log file
     */
    public function setLogPath($logPath, $permissions = 0644)
    {
        $this->logPath = '../../' . $this->logDir . $logPath;

        if (!file_exists($this->logPath)) {
            $this->createLogFile($this->logPath, $permissions);
        }
    }

    /**
     * Run the tasks added to the TaskRunner.
     */
    public function run()
    {
        $startTime = microtime(true); // Record start time
        $this->print("Starting TaskRunner.....");
        try {

            // Divide tasks into smaller chunks
            $chunks = array_chunk($this->tasks, $this->maxConcurrentTasks);

            // Process each chunk of tasks
            foreach ($chunks as $chunk) {
                $this->executeChunkOfTasks($chunk);

                // Sleep for a short interval before processing the next chunk
                usleep(10000);
            }

            // Wait until all processes are done
            while ($this->hasRunningProcesses()) {
                $this->waitForRunningTasks($runningProcesses, true); // Set a flag to indicate waiting for deadlock resolution
            }

            $endTime = microtime(true); // Record end time
            $this->print("All tasks have been run.....");
            $elapsedTime = number_format($endTime - $startTime, 2, '.', ''); // Calculate elapsed time
            $this->print("Total process time: {$elapsedTime} seconds.");
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            $this->print("An error occurred: " . $e->getMessage());
        }
    }

    /**
     * Execute a chunk of tasks concurrently.
     *
     * @param array $chunk Chunk of tasks to execute
     */
    private function executeChunkOfTasks($chunk)
    {
        $runningProcesses = [];
        foreach ($chunk as $task) {
            // Ensure maximum concurrent tasks limit is not exceeded
            while (count($runningProcesses) >= $this->maxConcurrentTasks) {
                $this->waitForRunningTasks($runningProcesses);
            }

            // Execute task and store the running process
            $runningProcesses[] = $this->executeTask($task);
        }

        // Wait for remaining tasks in the chunk to complete
        $this->waitForRunningTasks($runningProcesses, true); // Set a flag to indicate waiting for deadlock resolution
    }

    /**
     * Wait for running tasks to complete.
     *
     * @param array $runningProcesses Array of running processes
     * @param bool $resolveDeadlock Flag to indicate waiting for deadlock resolution
     */
    private function waitForRunningTasks(&$runningProcesses, $resolveDeadlock = false)
    {
        $startWaitingTime = microtime(true);

        // Loop until either all tasks are completed or deadlock resolution time is exceeded
        while (!empty($runningProcesses)) {
            foreach ($runningProcesses as $key => $processDetails) {
                $process = $processDetails['process'];
                $pid = $this->getPid($process);

                // Check if process is still running
                $isRunning = $this->isProcessRunning($process);
                // $this->print("TaskRunner - Process #{$pid} still running? " . ($isRunning ? "Yes" : "No"));

                // Check if process is still running
                if (!$isRunning) {
                    // Print task completion information
                    $this->printTaskCompletion($processDetails);
                    unset($runningProcesses[$key]); // Remove completed process
                } else {
                    // Check if the process has exceeded the timeout
                    $elapsedTime = microtime(true) - $processDetails['start_time'];
                    // Check if the process has exceeded the timeout
                    if ($elapsedTime > $this->processTimeout) {
                        if ($resolveDeadlock) {
                            $command = $processDetails['command'];
                            // Handle deadlock resolution here, e.g., by forcefully terminating the process
                            $this->print("TaskRunner - Timeout reached for PID: {$pid}, Task : {$command}. Handling deadlock...");
                            proc_terminate($process);
                            unset($runningProcesses[$key]); // Remove deadlock process
                        } else {
                            continue; // If deadlock resolution is not enabled, simply wait
                        }
                    }
                }
            }

            // If deadlock resolution is enabled and waiting time exceeds the resolution time, break the loop
            if ($resolveDeadlock && (microtime(true) - $startWaitingTime) > $this->processTimeout) {
                break;
            }
        }

        usleep(10000); // Sleep for a short interval
    }

    /**
     * Execute a task.
     *
     * @param array $task Task to execute
     * @return resource Process resource
     */
    private function executeTask($task)
    {
        $filePath = $this->jobsDir . $task['command'];
        $params = !empty($task['params']) ? implode(' ', $task['params']) : NULL;
        $descriptors = [
            ['pipe', 'r'],
            ['file', $this->logPath, 'a'],
            ['file', $this->logPath, 'a'],
        ];

        $command = "{$this->phpCommand} $filePath $params";

        try {
            // Open a process for the task
            $process = proc_open($command, $descriptors, $pipes);
            if (is_resource($process)) {
                // Add task to running tasks list
                $pid = $this->getPid($process);
                $this->print("TaskRunner - Start (PID: {$pid}) '$command'");
                $details = ['process' => $process, 'command' => $command, 'start_time' => microtime(true)];
                $this->runningTasks[$pid] = $details;
                return $details;
            } else {
                $this->print("Failed to start process for task '$command'.");
                return null;
            }
        } catch (\Exception $e) {
            $this->logError($e->getMessage());
            $this->print("An error occurred while executing the task: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a process is running.
     *
     * @param resource $process Process resource
     * @return bool
     */
    private function isProcessRunning($process)
    {
        // Check if $process is a valid resource
        if (!is_resource($process)) {
            return false;
        }

        // Check if the process is running
        $status = proc_get_status($process);

        // If $status is not an array or if the process status is not available,
        // consider the process as not running
        if (!is_array($status) || !isset($status['running'])) {
            return false;
        }

        return $status['running'];
    }

    /**
     * Check if there are any running processes.
     *
     * @return bool True if there are running processes, false otherwise
     */
    private function hasRunningProcesses()
    {
        return !empty($this->runningTasks);
    }

    /**
     * Print task completion information.
     *
     * @param array $task Task information
     */
    private function printTaskCompletion($task)
    {
        // Check if $task['process'] is a valid resource
        if (is_resource($task['process'])) {
            $pid = $this->getPid($task['process']);
            $pidInfo = $pid !== null ? "(PID: $pid)" : "Unknown PID";
            // Close the process if it's still running
            if ($pid !== null) {
                proc_close($task['process']);
            }

            // Calculate the time taken for the task
            $costSeconds = number_format(microtime(true) - $task['start_time'], 2, '.', '');

            $command = $task['command'];

            // Print task completion information
            $this->print("TaskRunner - Close $pidInfo '$command' | cost: {$costSeconds}s");
        }
    }

    /**
     * Get the PID of a process.
     *
     * @param resource $process Process resource
     * @return int PID
     */
    private function getPid($process)
    {
        // Check if $process is a valid resource
        if (!is_resource($process)) {
            return null;
        }

        // Retrieve the process status
        $status = proc_get_status($process);

        // If $status is not an array or if the PID is not available,
        // return null indicating that the PID is not available
        if (!is_array($status) || !isset($status['pid'])) {
            return null;
        }

        return $status['pid'];
    }

    /**
     * Print a text line.
     *
     * @param string $textLine Text to print
     */
    private function print($textLine)
    {
        echo $this->formatTextLine($textLine);
    }

    /**
     * Format a text line with timestamp.
     *
     * @param string $textLine Text to format
     * @return string Formatted text line
     */
    private function formatTextLine($textLine)
    {
        $formattedMessage = "[" . date("Y-m-d h:i A") . "] - $textLine\n";
        $workerLog = '../../' . $this->logDir . '/logs/TaskRunnerLog/' . date('Ymd') . '.log';
        $this->createLogFile($workerLog, 0644);
        file_put_contents($workerLog, $formattedMessage, FILE_APPEND);
        return $formattedMessage . PHP_EOL;
    }

    /**
     * Create a log file if it doesn't exist.
     *
     * @param string $logPath Path to the log file
     * @param int $permissions Permissions for the log file
     */
    private function createLogFile($logPath, $permissions)
    {
        $directory = dirname($logPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        touch($logPath);
        chmod($logPath, $permissions);
    }

    /**
     * Log an error.
     *
     * @param string $errorMessage Error message to log
     */
    private function logError($errorMessage)
    {
        if ($this->logPath !== null) {
            $errorLog = fopen($this->logPath, 'a');
            if ($errorLog !== false) {
                fwrite($errorLog, "[" . date("Y-m-d h:i A") . "] - ERROR: $errorMessage" . PHP_EOL);
                fclose($errorLog);
            }
        }
    }
}
