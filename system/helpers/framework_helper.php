<?php

use Sys\framework\Database\Database;
use Sys\framework\Validation;
use Sys\framework\Logger;
use Sys\framework\TaskRunner;

// DATABASE SECTION

function db($connection = 'default')
{
    global $config;

    try {
        $dbConfig = $config['db'];

        $dbObj = new Database('mysql');

        $dbObj->addConnection('default', $dbConfig['default']['development']);
        $dbObj->addConnection('slave', $dbConfig['slave']['development']);

        return $dbObj->connect($connection);
    } catch (Exception $e) {
        log_message('error', "db->" . __FUNCTION__ . "() : " . $e->getMessage());
        dd('Database connection : Failed to connect to database.');
    }
}

// VALIDATION SECTION

function validate($data, $rules, $customMessage = NULL)
{
    $valid = new Validation($data, $rules, $customMessage);
    return ['result' => $valid->validate(), 'error' => $valid->getError()];
}

// LOGGER SECTION

function log_message($type = 'info', $message = null)
{
    $logger = new Logger();

    // Depending on the provided type, call the corresponding log method
    switch ($type) {
        case 'debug':
            $logger->debug($message);
            break;
        case 'warning':
            $logger->warning($message);
            break;
        case 'info':
            $logger->info($message);
            break;
        case 'error':
            $logger->error($message);
            break;
        default:
            // If an invalid type is provided, log it as an error
            $logger->error("Invalid log message type: $type - $message");
            break;
    }
}

// TASK RUNNER SECTION

function TaskRunParallel()
{
    return new TaskRunner();
}
