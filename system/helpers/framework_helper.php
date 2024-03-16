<?php

use Sys\framework\Database;
use Sys\framework\Validation;
use Sys\framework\Logger;
use Sys\framework\TaskRunner;

function db($connection = 'default')
{
    if ($connection == 'default') {
        $dbCon = ConfigDB();
        if (!Database::getInstance())
            return new Database($dbCon['driver'] ?? 'mysql', $dbCon['hostname'] ?? 'localhost', $dbCon['username'] ?? 'root', $dbCon['password'], $dbCon['database'], $dbCon['port'], $dbCon['charset']);
        else
            return Database::getInstance();
    } else {
        return connect($connection);
    }
}

// DATABASE SECTION

function ConfigDB($connection = 'default')
{
    global $config;

    if (hasData($config['db'], $connection)) {
        return $config['db'][$connection][ENVIRONMENT];
    }

    exit('Database connection ' . $connection . ' not found');
}

function connect($connection = 'default')
{
    global $config;

    if (hasData($config['db'], $connection)) {
        $db = Database::getInstance();
        $db->connect($connection);
        $db->connection($connection);
        return $db;
    } else {
        exit("No connection with <b>'$connection'</b> name");
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
