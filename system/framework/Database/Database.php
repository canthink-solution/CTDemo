<?php

namespace Sys\framework\Database;

/**
 * Database Class
 *
 * @category  Database
 * @package   Sys\framework\Database
 * @author    
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   0.0.1
 */

class Database
{
    protected $db;

    /**
     * @var array The list of database support.
     */
    protected $listDatabasePlatSupport = [
        'mysql' => 'MySQL',
        'mssql' => 'MSSQL',
        'oci' => 'Oci',
        'mariadb' => 'MariaDB',
        'fdb' => 'Firebird',
        '-' => 'Unknown'
    ];

    public function __construct(string $driver = 'mysql')
    {
        if (empty($driver)) {
            throw new \InvalidArgumentException("No driver found.");
        }

        $dbPlatform = strtolower($driver);

        if (!isset($this->listDatabasePlatSupport[$dbPlatform])) {
            throw new \InvalidArgumentException("Unsupported database driver: {$dbPlatform}");
        }

        $driverClass = "Sys\\framework\\Database\\Drivers\\" . $this->listDatabasePlatSupport[$dbPlatform] . "Driver";
        $this->db = new $driverClass;
    }

    public function __call($method, $args)
    {
        if (method_exists($this->db, $method)) {
            return call_user_func_array([$this->db, $method], $args);
        }
        throw new \BadMethodCallException("Method $method does not exist");
    }
}