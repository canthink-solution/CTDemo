<?php

namespace Sys\framework;

/**
 * Database Class
 *
 * @category  Database Access
 * @package   Database
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   0.1.3
 */

use Sys\framework\Cache;

class Database
{
    /**
     * Static instance of self
     *
     * @var Database
     */
    protected static $_instance;

    /**
     * @var \PDO The PDO instance for database connection.
     */
    protected $pdo;

    /**
     * @var string $driver The database driver being used (e.g., 'mysql', 'oracle', etc.).
     */
    protected $driver = 'mysql';

    /**
     * @var array connections settings [profile_name=>[same_as_contruct_args]]
     */
    protected $connectionsSettings = array();

    /**
     * @var string the name of a default (main) pdo connection
     */
    public $connectionName = 'default';

    /**
     * @var string|null The database schema name.
     */
    protected $schema;

    /**
     * @var string|null The table name.
     */
    protected $table;

    /**
     * @var string The fields to select.
     */
    protected $fields = '*';

    /**
     * @var int|null The limit for the query.
     */
    protected $limit;

    /**
     * @var int|null The offset for the query.
     */
    protected $offset;

    /**
     * @var array|null The order by columns and directions.
     */
    protected $orderBy;

    /**
     * @var array|null The group by columns.
     */
    protected $groupBy;

    /**
     * @var string|null The conditions for WHERE clause.
     */
    protected $where = null;

    /**
     * @var string|null The join clauses.
     */
    protected $joins = null;

    /**
     * @var array The relations use for eager loading (N+1).
     */
    protected $relations = [];

    /**
     * @var array The previously executed error query
     */
    protected $_error;

    /**
     * @var bool The flag for sanitization.
     */
    protected $_secure = true;

    /**
     * @var array An array to store the bound parameters.
     */
    protected $_binds = [];

    /**
     * @var string The raw SQL query string.
     */
    protected $_query;

    /**
     * @var array An array to store profiling information (optional).
     */
    protected $_profiler = [];

    /**
     * @var string An string to store current active profiler
     */
    protected $_profilerActive = 'main';

    /**
     * @var string|null The cache key.
     */
    protected $cacheFile = null;

    /**
     * @var string|integer The cache to expire in seconds.
     */
    protected $cacheFileExpired = 1800;

    /**
     * Constructor.
     *
     * @param string $driver The database driver to be used (e.g., mysql, mssql, oracle, firebird).
     * @param string|null $host The host of the database server.
     * @param string|null $username The username for the database connection.
     * @param string|null $password The password for the database connection.
     * @param string|null $database The name of the database.
     * @param int|null $port The port number of the database server.
     * @param string|null $charset The character set for the database connection (default is 'utf8mb4').
     * @param string|null $socket The socket name or path to the Unix socket for the connection.
     */
    public function __construct($driver = 'mysql', $host = null, $username = null, $password = null, $database = null, $port = null, $charset = 'utf8mb4', $socket = null)
    {
        if (!in_array($driver, ['mysql', 'mssql', 'oracle', 'oci', 'firebird', 'fdb'])) {
            throw new \InvalidArgumentException("Invalid database driver '{$driver}' provided.");
        }

        if (!isset($this->connectionsSettings['default'])) {
            $this->addConnection(
                'default',
                array(
                    'driver' => $driver,
                    'host' => $host,
                    'username' => $username,
                    'password' => $password,
                    'db' => $database,
                    'port' => $port,
                    'charset' => $charset,
                    'socket' => $socket
                )
            );
        }

        if (!isset($this->pdo[$this->connectionName]))
            $this->connect($this->connectionName);

        self::$_instance = $this;
    }

    /**
     * A method to connect to the database
     *
     * @param string|null $connectionName
     *
     * @throws \Exception
     * @return void
     */
    public function connect($connectionName = 'default')
    {
        try {
            if (!isset($this->connectionsSettings[$connectionName])) {
                throw new \PDOException('Connection profile not set', 404);
            }

            $pro = $this->connectionsSettings[$connectionName];

            // Set default charset to utf8mb4 if not specified
            $charset = isset($pro['charset']) ? $pro['charset'] : 'utf8mb4';

            // Define database driver
            $this->driver = isset($pro['driver']) ? strtolower($pro['driver']) : 'mysql';

            // Build DSN (Data Source Name) based on database driver
            switch ($this->driver) {
                case 'mysql':
                    $dsn = "mysql:host={$pro['host']};dbname={$pro['db']};charset={$charset}";
                    if (isset($pro['port'])) {
                        $dsn .= ";port={$pro['port']}";
                    }
                    if (isset($pro['socket'])) {
                        $dsn .= ";unix_socket={$pro['socket']}";
                    }
                    break;
                case 'mssql':
                    $dsn = "sqlsrv:Server={$pro['host']};Database={$pro['db']}";
                    if (isset($pro['port']) && !empty($pro['port'])) {
                        $dsn .= ";port={$pro['port']}";
                    }
                    break;
                case 'oracle':
                case 'oci':
                    $dsn = "oci:dbname={$pro['host']}/{$pro['db']}";
                    break;
                case 'firebird':
                case 'fdb':
                    $dsn = "firebird:dbname={$pro['host']}";
                    break;
                default:
                    throw new \PDOException('Unsupported database driver', 404);
            }

            // Connection options
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
            ];

            // Specific options for MySQL
            if ($this->driver === 'mysql') {
                $options[\PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$charset}";
            }

            $pdo = new \PDO($dsn, $pro['username'], $pro['password'], $options);
            $this->pdo[$connectionName] = $pdo;
            $this->schema = $pro['db'];
        } catch (\PDOException $e) {
            $this->db_error_log($e, __FUNCTION__, 'Error connection');
        }
    }

    /**
     * Create & store a new PDO instance
     *
     * @param string $name
     * @param array  $params
     *
     * @return $this
     */
    public function addConnection($name, array $params)
    {
        $this->connectionsSettings[$name] = array();
        foreach (array('driver', 'host', 'username', 'password', 'db', 'port', 'socket', 'charset') as $k) {
            $prm = isset($params[$k]) ? $params[$k] : null;

            if ($k == 'host') {
                if (is_object($prm)) {
                    $this->pdo[$name] = $prm;
                }

                if (!is_string($prm)) {
                    $prm = null;
                }
            }

            $this->connectionsSettings[$name][$k] = $prm;
        }

        return $this;
    }

    /**
     * Set the connection name to use in the next query
     *
     * @param string $name
     *
     * @return $this
     * @throws \Exception
     */
    public function connection($name)
    {
        if (!isset($this->connectionsSettings[$name]))
            throw new \Exception('Connection ' . $name . ' was not added.');

        $this->connectionName = $name;
        return $this;
    }

    /**
     * A method to disconnect from the database
     *
     * @param string $connection Connection name to disconnect
     * @param bool $remove Flag indicating whether to remove connection settings
     *
     * @return void
     */
    public function disconnect($connection = 'default', $remove = false)
    {
        if (!isset($this->pdo[$connection])) {
            return;
        }

        $this->pdo[$connection] = null;
        unset($this->pdo[$connection]);

        if ($connection == $this->connectionName) {
            $this->connectionName = 'default';
        }

        // Remove connection settings if $remove is true
        if ($remove && isset($this->connectionsSettings[$connection])) {
            unset($this->connectionsSettings[$connection]);
        }
    }

    /**
     * A method of returning the static instance to allow access to the
     * instantiated object from within another class.
     * Inheriting this class would require reloading connection info.
     *
     * @uses $db = Database::getInstance();
     *
     * @return Database Returns the current instance.
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $query      The SQL query to execute.
     * @param array|null $bindParams Optional. An array of parameters to bind to the SQL statement.
     * @param string $fetch      Optional. The fetch mode for retrieving results. Default is 'get'.
     *                           Possible values: 'get' to fetch a single row, 'all' to fetch all rows.
     *
     * @return array|mixed|null Returns the fetched row(s) from the query.
     * @throws \Exception If an error occurs during query execution.
     */
    public function rawQuery($query, $bindParams = null, $fetch = 'get')
    {
        $this->_startProfiler(__FUNCTION__);
        $this->_profiler['profiling'][$this->_profilerActive]['binds'] = []; // Initialize the binds array
        try {
            // Expand asterisks in query
            $query = $this->_expandAsterisksInQuery($query);
            $stmt = $this->pdo[$this->connectionName]->prepare($query);
            $this->_profiler['query'] = $query;

            if ($bindParams !== null) {
                if (is_array($bindParams)) {
                    $this->_bindParams($stmt, $bindParams);
                } else {
                    throw new \PDOException('Bind parameters must be provided as an array', 400);
                }
            }

            // Generate the full query with binds
            $this->_generateFullQuery($query, $bindParams);

            $stmt->execute();
            $result = ($fetch === 'get') ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
        $this->_stopProfiler();

        return $result;
    }

    /**
     * Set the table name.
     *
     * @param string $table The table name.
     * @return $this
     * @throws \Exception if the table does not exist or if there's an error accessing the database.
     */
    public function table($table)
    {
        try {
            // Check if the table exists based on the database driver
            switch ($this->driver) {
                case 'mysql':
                    $query = "SHOW TABLES LIKE '$table'";
                    break;
                case 'mssql':
                    $query = "IF EXISTS (SELECT * FROM sysobjects WHERE name = '$table' AND xtype = 'U') SELECT 1";
                    break;
                case 'oracle':
                case 'oci':
                    $query = "SELECT table_name FROM user_tables WHERE table_name = '$table'";
                    break;
                case 'firebird':
                case 'fdb':
                    $query = "SELECT RDB\$RELATION_NAME FROM RDB\$RELATIONS WHERE RDB\$RELATION_NAME = '$table'";
                    break;
                default:
                    throw new \Exception("Unsupported database driver '{$this->driver}'", 500);
            }

            // Execute the query to check table existence
            $stmt = $this->pdo[$this->connectionName]->query($query);

            // Check if the table exists
            if ($stmt->fetchColumn() === false) {
                throw new \Exception("Table '$table' does not exist", 404);
            }

            // Assign the table name
            $this->table = trim($table);
            return $this;
        } catch (\PDOException $e) {
            $this->db_error_log($e, __FUNCTION__, 'Error accessing database');
        }
    }

    /**
     * Reset all parameters.
     *
     * @return $this
     */
    public function reset()
    {
        $this->driver = 'mysql';
        $this->connectionName = 'default';
        $this->table = null;
        $this->fields = '*';
        $this->limit = null;
        $this->offset = null;
        $this->orderBy = null;
        $this->groupBy = null;
        $this->where = null;
        $this->joins = null;
        $this->_error = [];
        $this->_secure = true;
        $this->_binds = [];
        $this->_query = [];
        $this->relations = [];
        $this->cacheFile = null;
        $this->cacheFileExpired = 1800;
        $this->_profilerActive = 'main';

        return $this;
    }

    /**
     * Set the fields to select.
     *
     * @param string|array $fields The fields to select. Can be either a string or an array.
     * @return $this
     */
    public function select($fields)
    {
        $this->fields = is_array($fields) ? implode(', ', $fields) : $fields;
        return $this;
    }

    /**
     * Set the limit for the query.
     *
     * @param int $limit The limit for the query.
     * @return $this
     * @throws \InvalidArgumentException If the $limit parameter is not an integer.
     */
    public function limit($limit)
    {
        // Try to cast the input to an integer
        $limit = filter_var($limit, FILTER_VALIDATE_INT);

        // Check if the input is not an integer after casting
        if ($limit === false) {
            throw new \InvalidArgumentException('Limit must be an integer.');
        }

        // Check if the input is less then 1
        if ($limit < 1) {
            throw new \InvalidArgumentException('Limit must be integer with higher then zero');
        }

        $this->limit = $limit;
        return $this;
    }

    /**
     * Set the offset for the query.
     *
     * @param int $offset The offset for the query.
     * @return $this
     * @throws \InvalidArgumentException If the $offset parameter is not an integer.
     */
    public function offset($offset)
    {
        // Try to cast the input to an integer
        $offset = filter_var($offset, FILTER_VALIDATE_INT);

        // Check if the input is not an integer after casting
        if ($offset === false) {
            throw new \InvalidArgumentException('Offset must be an integer.');
        }

        // Check if the input is less then 0
        if ($offset < 0) {
            throw new \InvalidArgumentException('Offset must be integer with higher or equal to zero');
        }

        $this->offset = $offset;
        return $this;
    }

    /**
     * Builds a where clause for the query.
     *
     * This method allows for building where clauses with various options.
     * You can either provide a single column name, value, operator, and where type
     * or an array of columns with their corresponding values.
     *
     * @param mixed $columnName The column name or an associative array of column names and values.
     * @param mixed $value (optional) The value to compare with the column.
     * @param string $operator (optional) The comparison operator (e.g., '=', '<', '>', '<>', '!=', 'LIKE'). Defaults to '='.
     * @param string $whereType (optional) The type of where clause (e.g., 'AND', 'OR'). Defaults to 'AND'.
     * @throws \InvalidArgumentException If $columnName is not a string or an associative array.
     *
     * @return $this
     */
    public function where($columnName, $value = NULL, $operator = '=', $whereType = 'AND')
    {
        try {

            if (!is_string($columnName) && !is_array($columnName)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string or an associative array.');
            }

            // Check if operator is supported
            $supportedOperators = ['=', '<', '>', '<=', '>=', '<>', '!=', 'LIKE'];
            if (!in_array($operator, $supportedOperators)) {
                throw new \InvalidArgumentException('Invalid operator. Supported operators are: ' . implode(', ', $supportedOperators));
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery([$columnName, $value], 'Full/Sub SQL statements are not allowed in where(). Please use whereRaw() function.');

            if (is_array($columnName)) {
                foreach ($columnName as $column => $val) {
                    if (!is_string($column)) {
                        throw new \InvalidArgumentException('Invalid column name in array. Must be a string.');
                    }
                    $this->_buildWhereClause($column, $val, $operator, $whereType);
                }
            } else {
                $this->_buildWhereClause($columnName, $value, $operator, $whereType);
            }

            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a WHERE clause for the query.
     *
     * This method allows for building where clauses with various options.
     * You can either provide a single column name, value, operator, and where type
     * or an array of columns with their corresponding values.
     *
     * @param mixed $columnName The column name or an associative array of column names and values.
     * @param mixed $value (optional) The value to compare with the column.
     * @param string $operator (optional) The comparison operator (e.g., '=', '<', '>', '<>', '!=', 'LIKE'). Defaults to '='.
     * @param string $whereType (optional) The type of where clause (e.g., 'AND', 'OR'). Defaults to 'OR'.
     * @throws \InvalidArgumentException If $columnName is not a string or an associative array.
     *
     * @return $this
     */
    public function orWhere($columnName, $value = NULL, $operator = '=', $whereType = 'OR')
    {
        try {

            if (!is_string($columnName) && !is_array($columnName)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string or an associative array.');
            }

            // Check if operator is supported
            $supportedOperators = ['=', '<', '>', '<=', '>=', '<>', '!=', 'LIKE'];
            if (!in_array($operator, $supportedOperators)) {
                throw new \InvalidArgumentException('Invalid operator. Supported operators are: ' . implode(', ', $supportedOperators));
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery([$columnName, $value], 'Full/Sub SQL statements are not allowed in orWere(). Please use whereRaw() function.');

            if (is_array($columnName)) {
                foreach ($columnName as $column => $val) {
                    if (!is_string($column)) {
                        throw new \InvalidArgumentException('Invalid column name in array. Must be a string.');
                    }
                    $this->_buildWhereClause($column, $val, $operator, $whereType);
                }
            } else {
                $this->_buildWhereClause($columnName, $value, $operator, $whereType);
            }

            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Add raw WHERE clause.
     *
     * This method allows for building where clauses with raw query.
     *
     * @param string $rawQuery The raw query string for where only.
     * @param array $value Optional. The bindings for the raw query.
     * @param string $whereType (optional) The type of where clause (e.g., 'AND', 'OR'). Defaults to 'AND'.
     * @throws \InvalidArgumentException If $rawQuery is not a string, an associative array, or a full SQL query.
     *
     * @return $this
     */
    public function whereRaw($rawQuery, $value = [], $whereType = 'AND')
    {
        try {
            if (!is_string($rawQuery)) {
                throw new \InvalidArgumentException('Invalid query format. Must be a string');
            }

            if (!empty($value) && !is_array($value)) {
                throw new \InvalidArgumentException("Value for 'whereRaw' must be an array");
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($rawQuery, 'Full/Sub SQL statements are not allowed in whereRaw(). Please use rawQuery() function.');

            $this->_buildWhereClause($rawQuery, $value, 'RAW', $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
            throw $e; // Rethrow the exception after logging it
        }
    }

    /**
     * Adds a BETWEEN condition to the WHERE clause.
     *
     * This function allows you to specify a range for a column using the
     * BETWEEN operator. It checks if the provided start and end values are integers,
     * doubles, or represent time formats. If valid, it builds the WHERE clause with
     * appropriate placeholders and the chosen `$whereType` (AND or OR).
     *
     * @param string $columnName The name of the column to compare.
     * @param mixed $start The lower bound of the range.
     * @param mixed $end The upper bound of the range.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @throws \InvalidArgumentException If the column name is not a string or the start/end values are invalid.
     * @return $this This object for method chaining.
     */
    public function whereBetween($columnName, $start, $end, $whereType = 'AND')
    {
        try {
            // Validate column name type
            if (!is_string($columnName)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Validate and format start and end values
            $formattedValues = [];
            foreach ([$start, $end] as $value) {
                if (is_int($value) || is_float($value)) {
                    // Numeric value: no formatting needed
                    $formattedValues[] = $value;
                } else if (preg_match('/^\d{1,4}-\d{2}-\d{2}$/', $value)) {
                    // Check for YYYY-MM-DD format (date)
                    $formattedValues[] = $this->pdo[$this->connectionName]->quote($value);
                } else if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                    // Check for HH:MM:SS format (time)
                    $formattedValues[] = $this->pdo[$this->connectionName]->quote($value);
                } else {
                    throw new \InvalidArgumentException('Invalid start or end value for BETWEEN. Must be numeric, date (YYYY-MM-DD), or time (HH:MM:SS).');
                }
            }

            // Ensure start is less than or equal to end for valid range
            if (!($formattedValues[0] <= $formattedValues[1])) {
                throw new \InvalidArgumentException('Start value must be less than or equal to end value for BETWEEN.');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($columnName, 'Full/Sub SQL statements are not allowed in whereBetween(). Please use whereRaw() function.');

            $this->_buildWhereClause($columnName, $formattedValues, 'BETWEEN', $whereType);

            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a NOT BETWEEN condition to the WHERE clause.
     *
     * This function allows you to specify a range for a column using the
     * NOT BETWEEN operator. It checks if the provided start and end values are integers,
     * doubles, or represent time formats. If valid, it builds the WHERE clause with
     * appropriate placeholders and the chosen `$whereType` (AND or OR).
     *
     * @param string $columnName The name of the column to compare.
     * @param mixed $start The lower bound of the range.
     * @param mixed $end The upper bound of the range.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @throws \InvalidArgumentException If the column name is not a string or the start/end values are invalid.
     * @return $this This object for method chaining.
     */
    public function whereNotBetween($columnName, $start, $end, $whereType = 'AND')
    {
        try {

            if (!is_string($columnName)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            $formattedValues = [];
            foreach ([$start, $end] as $value) {
                if (is_int($value) || is_float($value)) {
                    // Numeric value: no formatting needed
                    $formattedValues[] = $value;
                } else if (preg_match('/^\d{1,4}-\d{2}-\d{2}$/', $value)) {
                    // Check for YYYY-MM-DD format (date)
                    $formattedValues[] = $this->pdo[$this->connectionName]->quote($value);
                } else if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                    // Check for HH:MM:SS format (time)
                    $formattedValues[] = $this->pdo[$this->connectionName]->quote($value);
                } else {
                    throw new \InvalidArgumentException('Invalid start or end value for NOT BETWEEN. Must be numeric, date (YYYY-MM-DD), or time (HH:MM:SS).');
                }
            }

            // Ensure start is less than or equal to end for valid range
            if (!($formattedValues[0] <= $formattedValues[1])) {
                throw new \InvalidArgumentException('Start value must be less than or equal to end value for NOT BETWEEN.');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($columnName, 'Full/Sub SQL statements are not allowed in whereNotBetween(). Please use whereRaw() function.');

            $this->_buildWhereClause($columnName, $formattedValues, 'NOT BETWEEN', $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds an IN condition to the WHERE clause.
     *
     * This function allows you to specify a list of values for a column using
     * the IN operator. It builds the WHERE clause with appropriate placeholders
     * and the chosen `$whereType` (AND or OR).
     *
     * @param string $column The name of the column to compare.
     * @param array $value An array of values to check for inclusion.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @return $this This object for method chaining.
     */
    public function whereIn($column, $value, $whereType = 'AND')
    {
        try {

            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            if (!is_array($value)) {
                throw new \InvalidArgumentException("Value for 'IN' operator must be an array");
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereIn(). Please use whereRaw() function.');

            $this->_buildWhereClause($column, $value, 'IN', $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a NOT IN condition to the WHERE clause.
     *
     * This function allows you to specify a list of values for a column using
     * the NOT IN operator. It builds the WHERE clause with appropriate placeholders
     * and the chosen `$whereType` (AND or OR).
     *
     * @param string $column The name of the column to compare.
     * @param array $value An array of values to check for inclusion.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @return $this This object for method chaining.
     */
    public function whereNotIn($column, $value, $whereType = 'AND')
    {
        try {

            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            if (!is_array($value)) {
                throw new \InvalidArgumentException("Value for 'NOT IN' operator must be an array");
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereNotIn(). Please use whereRaw() function.');

            $this->_buildWhereClause($column, $value, 'NOT IN', $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds an IS NULL condition to the WHERE clause.
     *
     * This method checks if the specified column is NULL in the database.
     *
     * @param string $column The name of the column to check.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @return $this This object for method chaining.
     */
    public function whereNull($column, $whereType = 'AND')
    {
        try {

            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereNull(). Please use whereRaw() function.');

            $this->_buildWhereClause($column, null, 'IS NULL', $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds an IS NOT NULL condition to the WHERE clause.
     *
     * This method checks if the specified column is NOT NULL in the database.
     *
     * @param string $column The name of the column to check.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @return $this This object for method chaining.
     */
    public function whereNotNull($column, $whereType = 'AND')
    {
        try {

            // Validate column name type
            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereNotNull(). Please use whereRaw() function.');

            $this->_buildWhereClause($column, null, 'IS NOT NULL', $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a date comparison condition to the WHERE clause.
     *
     * This function allows you to filter based on specific date parts (year, month, day)
     * of a column. It converts the provided `$date` to a formatted Y-m-d string and uses
     * the appropriate database function (YEAR, MONTH, DAY) based on the configured database type.
     * 
     * @param string $column The name of the column to compare.
     * @param string $date The date to compare against (e.g., "2024-06-06").
     * @param string $operator (optional) The comparison operator (e.g., '=', '<', '>', '<=', '>=', '<>', '!='). Defaults to '='.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @throws \InvalidArgumentException If the column name is not a string, the date format is invalid, or the operator is not supported.
     * @return $this This object for method chaining.
     */
    public function whereDate($column, $date, $operator = '=', $whereType = 'AND')
    {
        try {

            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            // Check if date is valid
            $timestamp = strtotime($date);
            if ($timestamp === false) {
                throw new \InvalidArgumentException('Invalid date format. Date must be in a recognizable format. Suggested format : Y-m-d OR d-m-Y');
            }

            // Convert to Y-m-d format
            $formattedDate = date('Y-m-d', $timestamp);

            // Check if operator is supported
            $supportedOperators = ['=', '<', '>', '<=', '>=', '<>', '!='];
            if (!in_array($operator, $supportedOperators)) {
                throw new \InvalidArgumentException('Invalid operator. Supported operators are: ' . implode(', ', $supportedOperators));
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereDate(). Please use whereRaw() function.');

            $this->_buildWhereClause($this->_getDateFunction($column)['date'], $formattedDate, $operator, $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a month comparison condition to the WHERE clause.
     *
     * This function filters based on the month of the specified column. It validates the
     * provided `$month` to be a number between 1 and 12 and uses the appropriate database function
     * (MONTH) based on the configured database type.
     * 
     * @param string $column The name of the column to compare.
     * @param int $month The month number (between 1 and 12).
     * @param string $operator (optional) The comparison operator (e.g., '=', '<', '>', '<=', '>=', '<>', '!='). Defaults to '='.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @throws \InvalidArgumentException If the column name is not a string or the month is invalid.
     * @return $this This object for method chaining.
     */
    public function whereMonth($column, $month, $operator = '=', $whereType = 'AND')
    {
        try {

            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            if (!is_numeric($month) || $month < 1 || $month > 12) {
                throw new \InvalidArgumentException('Invalid month. Must be a number between 1 and 12.');
            }

            // Check if operator is supported
            $supportedOperators = ['=', '<', '>', '<=', '>=', '<>', '!='];
            if (!in_array($operator, $supportedOperators)) {
                throw new \InvalidArgumentException('Invalid operator. Supported operators are: ' . implode(', ', $supportedOperators));
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereMonth(). Please use whereRaw() function.');

            $this->_buildWhereClause($this->_getDateFunction($column)['month'], (int)$month, $operator, $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a day comparison condition to the WHERE clause.
     *
     * This function filters based on the day of the specified column. It validates the
     * provided `$day` to be a number between 1 and 31 and uses the appropriate database function
     * (DAY) based on the configured database type.
     * 
     * @param string $column The name of the column to compare.
     * @param int $day The day number (between 1 and 31).
     * @param string $operator (optional) The comparison operator (e.g., '=', '<', '>', '<=', '>=', '<>', '!='). Defaults to '='.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @throws \InvalidArgumentException If the column name is not a string or the day is invalid.
     * @return $this This object for method chaining.
     */
    public function whereDay($column, $day, $operator = '=', $whereType = 'AND')
    {
        try {

            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            if (!is_numeric($day) || $day < 1 || $day > 31) {
                throw new \InvalidArgumentException('Invalid day. Must be a number between 1 and 31.');
            }

            // Check if operator is supported
            $supportedOperators = ['=', '<', '>', '<=', '>=', '<>', '!='];
            if (!in_array($operator, $supportedOperators)) {
                throw new \InvalidArgumentException('Invalid operator. Supported operators are: ' . implode(', ', $supportedOperators));
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereDay(). Please use whereRaw() function.');

            $this->_buildWhereClause($this->_getDateFunction($column)['day'], (int)$day, $operator, $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a year comparison condition to the WHERE clause.
     *
     * This function filters based on the year of the specified column. It validates the
     * provided `$year` to be a number and uses the appropriate database function
     * (YEAR) based on the configured database type.
     * 
     * @param string $column The name of the column to compare.
     * @param int $year The year number.
     * @param string $operator (optional) The comparison operator (e.g., '=', '<', '>', '<=', '>=', '<>', '!='). Defaults to '='.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @throws \InvalidArgumentException If the column name is not a string or the year is invalid.
     * @return $this This object for method chaining.
     */
    public function whereYear($column, $year, $operator = '=', $whereType = 'AND')
    {
        try {
            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            if (!is_numeric($year) || strlen((string)$year) !== 4) {
                throw new \InvalidArgumentException('Invalid year. Must be a four-digit number.');
            }

            // Check if operator is supported
            $supportedOperators = ['=', '<', '>', '<=', '>=', '<>', '!='];
            if (!in_array($operator, $supportedOperators)) {
                throw new \InvalidArgumentException('Invalid operator. Supported operators are: ' . implode(', ', $supportedOperators));
            }

            // Ensure where type AND / OR
            if (!in_array($whereType, ['AND', 'OR'])) {
                throw new \InvalidArgumentException('Invalid where type. Supported operators are: AND/OR');
            }

            // Check if variable contains a full SQL statement
            $this->_forbidRawQuery($column, 'Full/Sub SQL statements are not allowed in whereYear(). Please use whereRaw() function.');

            $this->_buildWhereClause($this->_getDateFunction($column)['year'], (int)$year, $operator, $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Adds a where clause to search within a JSON column.
     *
     * @param string $columnName The name of the JSON column.
     * @param string $jsonPath The JSON path to search within.
     * @param mixed $value The value to search for.
     * @return $this
     */
    public function whereJsonContains($columnName, $jsonPath, $value)
    {
        // Check if the column is not null
        $this->whereNotNull($columnName);

        // Construct the JSON search condition based on the driver
        switch ($this->driver) {
            case 'mysql':
                $jsonCondition = "JSON_CONTAINS($columnName, '" . json_encode([$jsonPath => $value]) . "', '$')";
                break;
            case 'mssql':
                $jsonCondition = "JSON_VALUE($columnName, '$.\"$jsonPath\"') = '$value'";
                break;
            case 'oracle':
            case 'oci':
                // Oracle specific JSON path querying using JSON_EXISTS
                $jsonCondition = "JSON_EXISTS($columnName, 'strict $.$jsonPath?(@ == \"$value\")')";
                break;
            default:
                throw new \Exception("Unsupported database driver: " . $this->driver);
        }

        // Add the condition to the query builder
        $this->where($jsonCondition, null, 'JSON');
        return $this;
    }

    /**
     * Performs a join operation on the current query.
     *
     * This function allows you to join another table to the current query
     * based on specified columns and a join type.
     *
     * @param string $tableToJoin The name of the table to join.
     * @param string $foreign_key The column name in the joined table.
     * @param string $local_key The column name in the current table for reference.
     * @param string $joinType (optional) The type of join (e.g., 'LEFT', 'RIGHT', 'INNER'). Defaults to 'LEFT'.
     * @throws \InvalidArgumentException If any of the column names or join type are invalid.
     * @return $this This object for method chaining.
     */
    public function join($tableToJoin, $foreign_key, $local_key, $joinType = 'LEFT')
    {
        // Validate input parameters
        if (!is_string($tableToJoin) || !is_string($foreign_key) || !is_string($local_key)) {
            throw new \InvalidArgumentException('Invalid column names or table name provided.');
        }

        $validJoinTypes = ['INNER', 'LEFT', 'RIGHT', 'OUTER', 'LEFT OUTER', 'RIGHT OUTER'];
        if (!in_array(strtoupper($joinType), $validJoinTypes)) {
            throw new \InvalidArgumentException('Invalid join type. Valid types are: ' . implode(', ', $validJoinTypes));
        }

        if (empty($this->table)) {
            throw new \Exception('No table selected', 400);
        }

        // Build the join clause
        $this->joins .= " $joinType JOIN `$tableToJoin` ON `$tableToJoin`.`$foreign_key` = `$this->table`.`$local_key`";

        return $this;
    }

    /**
     * Set the order by columns and directions.
     *
     * @param string|array $columns The order by columns.
     * @param string $direction The order direction.
     * @return $this
     * @throws \InvalidArgumentException If the $direction parameter is not "ASC" or "DESC".
     */
    public function orderBy($columns, $direction = 'DESC')
    {
        // Check if direction is valid
        if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('Order direction must be "ASC" or "DESC".');
        }

        if (is_array($columns)) {
            foreach ($columns as $column => $dir) {
                $direction = strtoupper(!in_array(strtoupper($dir), ['ASC', 'DESC']) ? 'DESC' : $dir);
                $this->orderBy[] = "$column $direction"; // Push to the order by array
            }
        } else {
            $this->orderBy[] = "$columns $direction"; // Push a single order by clause
        }

        return $this;
    }

    /**
     * Set the order by with raw SQL expression.
     *
     * @param string $string The raw SQL expression for order by.
     * @param array|null $bindParams Optional. An array of parameters to bind to the SQL statement.
     * @return $this
     * @throws \InvalidArgumentException If the $string is empty.
     */
    public function orderByRaw($string, $bindParams = null)
    {
        // Check if string is empty
        if (empty($string)) {
            throw new \InvalidArgumentException('Order by cannot be null in `orderByRaw`.');
        }

        // Check if orderByRaw contains a full SQL statement
        $this->_forbidRawQuery($string, 'Full SQL statements are not allowed in `orderByRaw`.');

        // Store the raw order by string
        $this->orderBy[] = $string;

        if (!empty($bindParams)) {
            if (is_array($bindParams)) {
                $this->_binds = array_merge($this->_binds, $bindParams);
            } else {
                $this->_binds[] = $bindParams;
            }
        }

        return $this;
    }

    /**
     * Sets the GROUP BY clause for the query.
     *
     * This function allows you to specify one or more columns to group the results by.
     *
     * @param mixed $columns The column name(s) to group by. Can be a single string or an array of column names.
     * @throws \InvalidArgumentException If an invalid column name is provided.
     * @return $this This object for method chaining.
     */
    public function groupBy($columns)
    {
        if (is_string($columns)) {
            // Validate column name, Allow commas for multiple columns
            if (!preg_match('/^[a-zA-Z0-9._, ]+$/', $columns)) {
                throw new \InvalidArgumentException('Invalid column name(s) for groupBy.');
            }
            $this->groupBy = "$columns";
        } else if (is_array($columns)) {

            $groupBy = [];
            foreach ($columns as $column) {
                // Validate column name
                if (!preg_match('/^[a-zA-Z0-9._]+$/', $column)) {
                    throw new \InvalidArgumentException('Invalid column name in groupBy array.');
                }
                $groupBy[] = "`$column`";
            }

            $this->groupBy = implode(', ', $groupBy);
        } else {
            throw new \InvalidArgumentException('groupBy expects a string or an array of column names.');
        }

        return $this;
    }

    /**
     * Specify eager loading for a relationship using 'get' type.
     *
     * @param string $alias The alias for the relationship.
     * @param string $table The related table name.
     * @param string $foreign_key The foreign key column in the related table.
     * @param string $local_key The local key column in the current table.
     * @param Closure|null $callback An optional callback to customize the eager load.
     * @return $this
     */
    public function with($alias, $table, $foreign_key, $local_key, \Closure $callback = null)
    {
        $this->relations[$alias] = ['type' => 'get', 'details' => compact('table', 'foreign_key', 'local_key', 'callback')];
        return $this;
    }

    /**
     * Specify eager loading for a relationship using 'fetch' type.
     *
     * @param string $alias The alias for the relationship.
     * @param string $table The related table name.
     * @param string $foreign_key The foreign key column in the related table.
     * @param string $local_key The local key column in the current table.
     * @param Closure|null $callback An optional callback to customize the eager load.
     * @return $this
     */
    public function withOne($alias, $table, $foreign_key, $local_key, \Closure $callback = null)
    {
        $this->relations[$alias] = ['type' => 'fetch', 'details' => compact('table', 'foreign_key', 'local_key', 'callback')];
        return $this;
    }

    /**
     * Executes the built query and fetches results as associative arrays.
     *
     * This function prepares the built query string, binds any parameters,
     * executes the query, and fetches the results as associative arrays.
     * It also handles potential exceptions and profiler logging.
     *
     * @return array The fetched results as associative arrays.
     * @throws \PDOException If an error occurs during query execution.
     */
    public function get()
    {
        $result = null;

        // Build the final SELECT query string
        $this->_buildSelectQuery();

        $cachePrefix = 'get_';
        if (!empty($this->cacheFile)) {
            $result = $this->_getCacheData($cachePrefix . $this->cacheFile);
        }

        if (empty($result)) {

            // Start profiler for performance measurement 
            $this->_startProfiler(__FUNCTION__);

            // Prepare the query statement
            $stmt = $this->pdo[$this->connectionName]->prepare($this->_query);

            // Bind parameters if any
            if (!empty($this->_binds)) {
                $this->_bindParams($stmt, $this->_binds);
            }

            try {
                // Log the query for debugging 
                $this->_profiler['profiling'][$this->_profilerActive]['query'] = $this->_query;

                // Generate the full query string with bound values 
                $this->_generateFullQuery($this->_query, $this->_binds);

                // Execute the prepared statement
                $stmt->execute();

                // Fetch all results as associative arrays
                $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Log database errors
                $this->db_error_log($e, __FUNCTION__);
                throw $e; // Re-throw the exception
            }

            // Stop profiler 
            $this->_stopProfiler();

            // Save connection name, relations & caching info temporarily
            $_temp_connection = $this->connectionName;
            $_temp_relations = $this->relations;
            $_temp_cacheKey = $this->cacheFile;
            $_temp_cacheExpired = $this->cacheFileExpired;

            // Reset internal properties for next query
            $this->reset();

            // Process eager loading if implemented 
            if (!empty($result) && !empty($_temp_relations)) {
                $result = $this->_processEagerLoading($result, $_temp_relations, $_temp_connection, 'get');
            }

            if (!empty($_temp_cacheKey) && !empty($result)) {
                $this->_setCacheData($cachePrefix . $_temp_cacheKey, $result, $_temp_cacheExpired);
            }

            unset($_temp_connection, $_temp_relations, $_temp_cacheKey, $_temp_cacheExpired, $cachePrefix);
        }

        return $result;
    }

    /**
     * Executes the built query and fetches the first result as an associative array.
     *
     * This function behaves similarly to `get`, but it fetches only the first
     * result as an associative array and returns it. This is useful for cases
     * where you only need a single record.
     *
     * @return mixed The first fetched result as an associative array, or null if no results found.
     * @throws \PDOException If an error occurs during query execution.
     */
    public function fetch()
    {
        $result = null;

        // Set limit to 1 to ensure only 1 data return
        $this->limit(1);

        // Build the final SELECT query string
        $this->_buildSelectQuery();

        $cachePrefix = 'fetch_';
        if (!empty($this->cacheFile)) {
            $result = $this->_getCacheData($cachePrefix . $this->cacheFile);
        }

        if (empty($result)) {

            // Start profiler for performance measurement
            $this->_startProfiler(__FUNCTION__);

            // Prepare the query statement
            $stmt = $this->pdo[$this->connectionName]->prepare($this->_query);

            // Bind parameters if any
            if (!empty($this->_binds)) {
                $this->_bindParams($stmt, $this->_binds);
            }

            try {
                // Log the query for debugging
                $this->_profiler['profiling'][$this->_profilerActive]['query'] = $this->_query;

                // Generate the full query string with bound values
                $this->_generateFullQuery($this->_query, $this->_binds);

                // Execute the prepared statement
                $stmt->execute();

                // Fetch only the first result as an associative array
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Log database errors
                $this->db_error_log($e, __FUNCTION__);
                throw $e; // Re-throw the exception
            }

            // Stop profiler
            $this->_stopProfiler();

            // Save connection name, relations & caching info temporarily
            $_temp_connection = $this->connectionName;
            $_temp_relations = $this->relations;
            $_temp_cacheKey = $this->cacheFile;
            $_temp_cacheExpired = $this->cacheFileExpired;

            // Reset internal properties for next query
            $this->reset();

            // Process eager loading if implemented 
            if (!empty($result) && !empty($_temp_relations)) {
                $result = $this->_processEagerLoading($result, $_temp_relations, $_temp_connection, 'fetch');
            }

            if (!empty($_temp_cacheKey) && !empty($result)) {
                $this->_setCacheData($cachePrefix . $_temp_cacheKey, $result, $_temp_cacheExpired);
            }

            unset($_temp_connection, $_temp_relations, $_temp_cacheKey, $_temp_cacheExpired, $cachePrefix);
        }

        // Return the first result or null if not found
        return $result;
    }

    /**
     * Retrieves and paginates the results of a query.
     * 
     * This method fetches data from the database and applies pagination based on the provided parameters.
     * 
     * @param int $currentPage (optional) The current page number (defaults to 1).
     * @param int $limit (optional) The number of records to retrieve per page (defaults to 10).
     * @param int $draw (optional) An identifier used for request tracking in server-side processing (defaults to 1).
     * @return array The paginated query results as an array.
     * @throws \Exception If there's an error accessing the database or if the table does not exist.
     */
    public function paginate($currentPage = 1, $limit = 10, $draw = 1)
    {
        // Reset the offset & limit to ensure the $this->_query not generate with that when call _buildSelectQuery() function
        $this->offset = $this->limit = null;

        // Build the final SELECT query string
        $this->_buildSelectQuery();

        // Start profiler for performance measurement 
        $this->_startProfiler(__FUNCTION__);

        try {

            // Calculate offset
            $offset = ($currentPage - 1) * $limit;

            // Get total count
            $total = $this->count();

            // Calculate total pages
            $totalPages = ceil($total / $limit);

            // Add LIMIT and OFFSET clauses to the main query
            switch ($this->driver) {
                case 'mysql':
                    $this->_query .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;
                    break;
                case 'mssql':
                    $this->_query = "SELECT * FROM (SELECT *, ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS RowNum FROM (" . $this->_query . ") AS innerQuery) AS outerQuery WHERE RowNum BETWEEN $offset + 1 AND $offset + $limit";
                    break;
                case 'oracle':
                case 'oci':
                    $this->_query = 'SELECT * FROM (SELECT innerQuery.*, ROWNUM AS rn FROM (' . $this->_query . ') innerQuery WHERE ROWNUM <= ' . ($offset + $limit) . ') WHERE rn > ' . $offset;
                    break;
                default:
                    throw new \Exception('Unsupported database driver for paginate()');
            }

            // Execute the main query
            $stmt = $this->pdo[$this->connectionName]->prepare($this->_query);

            // Bind parameters if any
            if (!empty($this->_binds)) {
                $this->_bindParams($stmt, $this->_binds);
            }

            // Log the query for debugging 
            $this->_profiler['profiling'][$this->_profilerActive]['query'] = $this->_query;

            // Generate the full query string with bound values 
            $this->_generateFullQuery($this->_query, $this->_binds);

            // Execute the prepared statement
            $stmt->execute();

            // Fetch the result in associative array
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Calculate next page
            $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

            // Calculate previous page
            $previousPage = ($currentPage > 1) ? $currentPage - 1 : null;

            // Adjust array keys to start from previous count
            $startIndex = ($currentPage - 1) * $limit;

            if (!empty($result)) {
                $result = array_combine(range($startIndex, $startIndex + count($result) - 1), $result);
            }

            $paginate = [
                'draw' => $draw,
                'recordsTotal' => $total ?? 0,
                'recordsFiltered' => count($result) ?? 0,
                'data' => $result ?? null,
                'current_page' => $currentPage,
                'next_page' => $nextPage,
                'previous_page' => $previousPage,
                'last_page' => $totalPages,
                'error' => $currentPage > $totalPages ? "current page ({$currentPage}) is more than total page ({$totalPages})" : ''
            ];
        } catch (\PDOException $e) {
            // Log database errors
            $this->db_error_log($e, __FUNCTION__);
            throw $e; // Re-throw the exception
        }

        // Stop profiler 
        $this->_stopProfiler();

        // Save connection name and relations temporarily
        $_temp_connection = $this->connectionName;
        $_temp_relations = $this->relations;

        // Reset internal properties for next query
        $this->reset();

        // Process eager loading if implemented 
        if (!empty($paginate['data']) && !empty($_temp_relations)) {
            $paginate['data'] = $this->_processEagerLoading($paginate['data'], $_temp_relations, $_temp_connection, 'get');
        }

        unset($_temp_connection, $_temp_relations);

        return $paginate;
    }

    /**
     * Retrieves the total count of rows that would be returned by the current query.
     *
     * This function executes a separate query based on the current state of `$this->_query` to efficiently calculate the total number of rows without fetching the actual data.
     * The `ORDER BY` clause (if present) is removed from the query for accurate counting.
     * 
     * @return int The total number of rows that would be returned by the current query.
     * @throws \PDOException If there's an error accessing the database.
     */
    public function count()
    {
        try {

            // Start profiler for performance measurement
            $this->_startProfiler(__FUNCTION__);

            // Check if query is empty then generate it first.
            if (empty($this->_query)) {
                $this->_buildSelectQuery();
            }

            // Create a separate query to get total count
            switch ($this->driver) {
                case 'mysql':
                case 'pgsql':
                case 'oracle':
                case 'oci':
                    $sqlTotal = 'SELECT COUNT(*) count ' . preg_replace('/\s+ORDER BY\s+.*?(?=\s+LIMIT|\s+OFFSET|\s+GROUP BY|$)/i', '', substr($this->_query, strpos($this->_query, 'FROM')));
                    break;
                case 'mssql':
                    $sqlTotal = 'SELECT COUNT(*) count FROM (' . preg_replace('/\s+ORDER BY\s+.*?(?=\s+LIMIT|\s+OFFSET|\s+GROUP BY|$)/i', '', $this->_query) . ') AS subquery';
                    break;
                default:
                    throw new \Exception('Unsupported database driver for count()');
            }

            // Execute the total count query
            $stmtTotal = $this->pdo[$this->connectionName]->prepare($sqlTotal);

            // Bind parameters if any
            if (!empty($this->_binds)) {
                $this->_bindParams($stmtTotal, $this->_binds);
            }

            $stmtTotal->execute();
            $totalResult = $stmtTotal->fetch(\PDO::FETCH_ASSOC);

            // Stop profiler
            $this->_stopProfiler();

            return $totalResult['count'] ?? 0;
        } catch (\PDOException $e) {
            // Log database errors
            $this->db_error_log($e, __FUNCTION__);
            throw $e; // Re-throw the exception
        }
    }

    /**
     * Execute the query in chunks.
     *
     * @param int $size The size of each chunk.
     * @param callable $callback The callback to handle each chunk.
     */
    public function chunk($size, callable $callback)
    {
        $offset = 0;

        // Set the temporary data to holds the original value
        $_tempConnection = $this->connectionName;
        $_tempTable = $this->table;
        $_tempFields = $this->fields;
        $_tempOrderBy = $this->orderBy;
        $_tempGroupBy = $this->groupBy;
        $_tempWhere = $this->where;
        $_tempJoins = $this->joins;
        $_tempBinds = $this->_binds;
        $_tempRelation = $this->relations;

        while (true) {

            // Set back to original value for next details
            $this->connectionName = $_tempConnection;
            $this->table = $_tempTable;
            $this->fields = $_tempFields;
            $this->orderBy = $_tempOrderBy;
            $this->groupBy = $_tempGroupBy;
            $this->where = $_tempWhere;
            $this->joins = $_tempJoins;
            $this->_binds = $_tempBinds;
            $this->relations = $_tempRelation;

            
            $this->limit($size)->offset($offset);
            $this->_setProfilerIdentifier('chunk_size' . $size . '_offset' . $offset);
            $results = $this->get();

            if (empty($results)) {
                break;
            }

            if (call_user_func($callback, $results) === false) {
                break;
            }

            $offset += $size;
        }

        // Unset the variables to free memory
        unset($_tempConnection, $_tempTable, $_tempFields, $_tempOrderBy, $_tempGroupBy, $_tempWhere, $_tempJoins, $_tempBinds, $_tempRelation);

        // Reset internal properties for next query
        $this->reset();

        return $this;
    }

    /**
     * Builds a WHERE clause fragment based on provided conditions.
     *
     * This function is used internally to construct WHERE clause parts based on
     * column name, operator, value(s), and WHERE type (AND or OR). It handles
     * different operators like `=`, `IN`, `NOT IN`, `BETWEEN`, and `NOT BETWEEN`.
     * It uses placeholders (`?`) for values and builds the appropriate clause structure.
     * This function also merges the provided values into the internal `_binds` array
     * for later binding to the prepared statement.
     *
     * @param string $columnName The name of the column to compare.
     * @param mixed $value The value or an array of values for the comparison.
     * @param string $operator (optional) The comparison operator (e.g., =, IN, BETWEEN). Defaults to =.
     * @param string $whereType (optional) The type of WHERE clause (AND or OR). Defaults to AND.
     * @throws \InvalidArgumentException If invalid operator or value format is provided.
     */
    private function _buildWhereClause($columnName, $value = null, $operator = '=', $whereType = 'AND')
    {
        if (!isset($this->where)) {
            $this->where = "";
        } else {
            $this->where .= " $whereType ";
        }

        $placeholder = '?'; // Use a single placeholder for all conditions

        switch ($operator) {
            case 'IN':
            case 'NOT IN':
                if (!is_array($value)) {
                    throw new \InvalidArgumentException('Value for IN or NOT IN operator must be an array');
                }
                $this->where .= "$columnName $operator (" . implode(',', array_fill(0, count($value), $placeholder)) . ")";
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                if (!is_array($value) || count($value) !== 2) {
                    throw new \InvalidArgumentException("Value for 'BETWEEN' or 'NOT BETWEEN' operator must be an array with two elements (start and end)");
                }
                $this->where .= "($columnName $operator $placeholder AND $placeholder)";
                break;
            case 'JSON':
                $this->where .= "$columnName";
                break;
            case 'IS NULL':
            case 'IS NOT NULL':
                $this->where .= "$columnName $operator";
                break;
            case 'RAW':
                $this->where .= "($columnName)";
                break;
            default:
                $this->where .= "$columnName $operator $placeholder";
                break;
        }

        if (!empty($value)) {
            if (is_array($value)) {
                $this->_binds = array_merge($this->_binds, $value);
            } else {
                $this->_binds[] = $value;
            }
        }
    }

    /**
     * Builds the final SELECT query string based on the configured options.
     *
     * This function combines all the query components like selected fields, table,
     * joins, WHERE clause, GROUP BY, ORDER BY, and LIMIT into a single SQL query string.
     *
     * @return $this This object for method chaining.
     * @throws \InvalidArgumentException If an asterisk (*) is used in the select clause
     *                                   and no table is specified.
     */
    private function _buildSelectQuery()
    {
        // Build the basic SELECT clause with fields
        $this->_query = "SELECT " . ($this->fields === '*' ? '*' : $this->fields) . " FROM ";

        // Append table name with schema (if provided)
        if (empty($this->schema)) {
            $this->_query .= "`$this->table`";
        } else {
            $this->_query .= "`$this->schema`.`$this->table`";
        }

        // Add JOIN clauses if available
        if ($this->joins) {
            $this->_query .= $this->joins;
        }

        // Add WHERE clause if conditions exist
        if ($this->where) {
            $this->_query .= " WHERE " . $this->where;
        }

        // Add GROUP BY clause if specified
        if ($this->groupBy) {
            $this->_query .= " GROUP BY " . $this->groupBy;
        }

        // Add ORDER BY clause if specified
        if ($this->orderBy) {
            $orderBy = implode(', ', $this->orderBy);
            $this->_query .= " ORDER BY " . $orderBy;
        }

        // Add LIMIT clause if specified
        if ($this->limit) {
            switch ($this->driver) {
                case 'mysql':
                    $this->_query .= " LIMIT " . $this->limit;
                    break;
                case 'mssql':
                    $this->_query .= " TOP (" . $this->limit . ") ";
                    break;
                case 'oracle':
                case 'oci':
                    $this->_query .= " ROWNUM <= " . ($this->limit);
                    break;
                case 'firebird':
                    $this->_query .= " FETCH FIRST " . $this->limit . " ROWS ONLY";
                    break;
                default:
                    throw new \Exception("LIMIT clause not supported for driver: " . $this->driver);
            }
        }

        // Add OFFSET clause if offset is set
        if ($this->offset) {
            $this->_query .= " OFFSET " . $this->offset;
        }

        // Expand asterisks in the query (replace with actual column names)
        $this->_query = $this->_expandAsterisksInQuery($this->_query);

        return $this;
    }

    /**
     * Enable or disable secure input.
     *
     * @param bool $secure Whether to enable or disable secure input.
     * @return $this
     */
    public function secureInput($secure = true)
    {
        $this->_secure = $secure;
        return $this;
    }

    # EAGER LOADER SECTION

    /**
     * Load relations and attach them to the main data efficiently.
     *
     * @param array $data The result for the main query/subquery.
     * @param array $relations The relations to be loaded.
     * @param string $connectionName The database connection name.
     * @param string $typeFetch The fetch type ('fetch' or 'get').
     */
    private function _processEagerLoading(&$data, $relations, $connectionName, $typeFetch)
    {
        $data = $typeFetch == 'fetch' ? [$data] : $data;
        $connectionObj = Database::$_instance->connection($connectionName);

        foreach ($relations as $alias => $eager) {

            $method = $eager['type']; // Get the type (get or fetch)
            $config = $eager['details']; // Get the configuration details

            $table = $config['table']; // Table name of the related data
            $fk_id = $config['foreign_key']; // Foreign key column in the related table
            $pk_id = $config['local_key']; // Local key column in the current table
            $callback = $config['callback']; // Optional callback for customizing the query

            // Extract all primary keys from the main result set
            $primaryKeys = array_values(array_unique(array_column($data, $pk_id), SORT_REGULAR));

            // Check if batch processing is needed
            $batchSize = 1000; // Adjust this threshold as needed
            if (count($primaryKeys) > $batchSize) {
                $this->_processEagerLoadingInBatches($data, $primaryKeys, $batchSize, $table, $fk_id, $pk_id, $connectionName, $method, $alias, $callback);
            } else {
                // Fetch related records using a single query
                $relatedRecordsQuery = $connectionObj->table($table)->whereIn($fk_id, $primaryKeys);

                // Apply callback if provided for customization
                if ($callback instanceof \Closure) {
                    $callback($relatedRecordsQuery);
                }

                // Set profiler
                $this->_setProfilerIdentifier('with_' . $alias);

                // Fetch data 
                $relatedRecords = $relatedRecordsQuery->get();

                // Logic to process and attach data to main/subquery data
                $this->attachEagerLoadedData($method, $data, $relatedRecords, $alias, $fk_id, $pk_id);
            }
        }

        return $typeFetch == 'fetch' ? $data[0] : $data;
    }

    /**
     * Process eager loading for a large dataset in batches.
     *
     * This function splits the primary keys into chunks and fetches related
     * data for each chunk in separate queries.
     *
     * @param array $data The main result data.
     * @param array $primaryKeys The array of primary keys from the main data.
     * @param int $batchSize The maximum number of primary keys per batch.
     * @param string $table The related table name.
     * @param string $fk_id The foreign key column in the related table.
     * @param string $pk_id The local key column in the current table.
     * @param string $connectionName The database connection name.
     * @param string $method The method type ('get' or 'fetch').
     * @param string $alias The alias for the relationship.
     * @param Closure|null $callback An optional callback to customize the query.
     */
    private function _processEagerLoadingInBatches(&$data, $primaryKeys, $batchSize, $table, $fk_id, $pk_id, $connectionName, $method, $alias, \Closure $callback = null)
    {
        $connectionObj = Database::$_instance->connection($connectionName);

        $chunks = array_chunk($primaryKeys, $batchSize);

        // Initialize an empty array to store all related records
        $allRelatedRecords = [];

        foreach ($chunks as $key => $chunk) {
            $relatedRecordsQuery = $connectionObj->table($table)->whereIn($fk_id, $chunk);

            // Apply callback if provided for customization
            if ($callback instanceof \Closure) {
                $callback($relatedRecordsQuery);
            }

            // Set profiler
            $this->_setProfilerIdentifier('with_' . $alias . '_' . ($key + 1));

            // Fetch data 
            $chunkRelatedRecords = $relatedRecordsQuery->get();

            // Merge chunk results into the allRelatedRecords array
            $allRelatedRecords = array_merge($allRelatedRecords, $chunkRelatedRecords);
        }

        // Attach related data to the main data
        $this->attachEagerLoadedData($method, $data, $allRelatedRecords, $alias, $fk_id, $pk_id);
    }

    /**
     * Helper function to attach related data to the main result set.
     *
     * @param string $method The method type ('get' or 'fetch').
     * @param array $data The result for the main query/subquery.
     * @param array $relatedRecords The fetched related data.
     * @param string $alias The alias for the relationship.
     * @param string $fk_id The foreign key column in the related table.
     * @param string $pk_id The local key column in the current table.
     */
    private function attachEagerLoadedData($method, &$data, &$relatedRecords, $alias, $fk_id, $pk_id)
    {
        // Organize related records by foreign key using an associative array
        $relatedMap = [];
        foreach ($relatedRecords as $relatedRow) {
            $relatedMap[$relatedRow[$fk_id]][] = $relatedRow;
        }

        // Attach related data to the main data set
        foreach ($data as &$row) {
            $row[$alias] = $method === 'fetch' && isset($relatedMap[$row[$pk_id]])
                ? $relatedMap[$row[$pk_id]][0]
                : ($relatedMap[$row[$pk_id]] ?? []);
        }
    }

    # PROFILER SECTION

    /**
     * Returns the internal profiler data.
     *
     * This function allows you to access the profiler information collected
     * during query execution, including method name, start and end times, query,
     * binds, execution time, and status.
     *
     * @return array The profiler data.
     */
    public function profiler()
    {
        return $this->_profiler;
    }

    /**
     * Sets the active profiler identifier.
     *
     * This function allows you to designate a specific profiler instance within the
     * `_profilers` array to be used for subsequent profiling operations. By
     * default, the profiler with the identifier 'main' is activated.
     *
     * Using this function enables you to manage and track data for multiple concurrent
     * profiling sessions within your application.
     *
     * @param string $identifier (optional) A unique identifier for the profiler to activate.
     *                             Defaults to 'main' if not provided.
     *
     * @return string The currently active profiler identifier.
     *
     */
    private function _setProfilerIdentifier($identifier = 'main')
    {
        $this->_profilerActive = $identifier;
        return $this->_profilerActive;
    }

    /**
     * Starts the profiler for a specific method.
     *
     * This function initializes the profiler data structure when a query building
     * method is called. It stores the method name, start time, and formatted start time.
     *
     * @param string $method The name of the method that initiated profiling.
     */
    private function _startProfiler($method)
    {
        $startTime = microtime(true);

        // Get PHP version
        $this->_profiler['php'] = phpversion();  // Simpler approach for version string

        // Get OS version
        if (function_exists('php_uname')) {
            $this->_profiler['os'] = php_uname('s') . ' ' . php_uname('r');  // OS and release
        } else {
            // Handle cases where php_uname is not available
            $this->_profiler['os'] = 'Unknown';
        }

        // Get database version (assuming a PDO connection)
        if (isset($this->pdo[$this->connectionName]) && $this->pdo[$this->connectionName] instanceof \PDO) {
            $this->_profiler['database'] = $this->pdo[$this->connectionName]->getAttribute(\PDO::ATTR_SERVER_VERSION);
        } else {
            // Handle cases where no database connection exists
            $this->_profiler['database'] = 'Unknown';
        }

        $this->_profiler['profiling'][$this->_profilerActive] = [
            'method' => $method,
            'start' => $startTime,
            'start_formatted' => date('Y-m-d h:i A', (int) $startTime),
            'query' => null,
            'binds' => null,
            'end' => null,
            'end_formatted' => null,
            'execution_time' => null,
            'status' => null,
            'memory_usage' => memory_get_usage(),
            'memory_usage_peak' => memory_get_peak_usage()
        ];
    }

    /**
     * Stops the profiler and calculates execution time and status.
     *
     * This function is called after query execution. It calculates the execution
     * time, formats it, and sets the execution status based on predefined thresholds.
     * It also updates the profiler data with end time, formatted end time, execution time, and status.
     * 
     */
    private function _stopProfiler()
    {
        if (!isset($this->_profiler['profiling'][$this->_profilerActive])) {
            return;  // Profiler not started
        }

        $endTime = microtime(true);
        $executionTime = $endTime - $this->_profiler['profiling'][$this->_profilerActive]['start'];

        $this->_profiler['profiling'][$this->_profilerActive]['memory_usage'] = $this->_formatBytes(memory_get_usage() - $this->_profiler['profiling'][$this->_profilerActive]['memory_usage'], 2);
        $this->_profiler['profiling'][$this->_profilerActive]['memory_usage_peak'] = $this->_formatBytes(memory_get_peak_usage() - $this->_profiler['profiling'][$this->_profilerActive]['memory_usage_peak'], 4);

        $this->_profiler['profiling'][$this->_profilerActive]['end'] = $endTime;
        $this->_profiler['profiling'][$this->_profilerActive]['end_formatted'] = date('Y-m-d h:i A', (int) $endTime);

        // Calculate and format execution time with milliseconds
        $milliseconds = round(($executionTime - floor($executionTime)) * 1000, 2);
        $totalSeconds = floor($executionTime);
        $seconds = $totalSeconds % 60;
        $minutes = floor(($totalSeconds % 3600) / 60);
        $hours = floor($totalSeconds / 3600);

        $formattedExecutionTime = '';
        if ($totalSeconds == 0) {
            $formattedExecutionTime = sprintf("%dms", $milliseconds);
        } else if ($hours > 0) {
            $formattedExecutionTime = sprintf("%dh %dm %ds %dms", $hours, $minutes, $seconds, $milliseconds);
        } else if ($minutes > 0) {
            $formattedExecutionTime = sprintf("%dm %ds %dms", $minutes, $seconds, $milliseconds);
        } else {
            $formattedExecutionTime = sprintf("%ds %dms", $seconds, $milliseconds);
        }

        $this->_profiler['profiling'][$this->_profilerActive]['execution_time'] = $formattedExecutionTime;
        $this->_profiler['stack_trace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8); // Capture starting stack trace

        // Set execution status based on predefined thresholds
        $this->_profiler['profiling'][$this->_profilerActive]['status'] = ($executionTime >= 3.5) ? 'very slow' : (($executionTime >= 1.5 && $executionTime < 3.5) ? 'slow' : (($executionTime > 0.5 && $executionTime < 1.49) ? 'fast' : 'very fast'));

        // Removed unused profiler from being display & free resources
        unset(
            $endTime,
            $executionTime,
            $formattedExecutionTime,
            $this->_profiler['profiling'][$this->_profilerActive]['binds'],
            $this->_profiler['profiling'][$this->_profilerActive]['start'],
            $this->_profiler['profiling'][$this->_profilerActive]['end'],
        );
    }

    # HELPER SECTION

    /**
     * Begin a transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->pdo[$this->connectionName]->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @return void
     */
    public function commit()
    {
        $this->pdo[$this->connectionName]->commit();
    }

    /**
     * Rollback a transaction.
     *
     * @return void
     */
    public function rollback()
    {
        $this->pdo[$this->connectionName]->rollBack();
    }

    /**
     * Caches data with an expiration time.
     *
     * This method sets a cache entry identified by the provided key with optional expiration time.
     *
     * @param string $key The unique key identifying the cache entry.
     * @param int $expire The expiration time of the cache entry in seconds (default: 1800 seconds).
     * @return $this Returns the current object instance for method chaining.
     */
    public function cache($key, $expire = 1800)
    {
        $this->cacheFile = $key;
        $this->cacheFileExpired = $expire;

        return $this;
    }

    /**
     * Retrieves cached data for the given key.
     *
     * This method retrieves cached data identified by the provided key using the Cache class.
     *
     * @param string $key The unique key identifying the cached data.
     * @return mixed|null Returns the cached data if found; otherwise, returns null.
     */
    private function _getCacheData($key)
    {
        $cache = new Cache();
        return $cache->get($key);
    }

    /**
     * Stores data in the cache with an optional expiration time.
     *
     * This method stores data identified by the provided key in the cache using the Cache class.
     *
     * @param string $key The unique key identifying the cache entry.
     * @param mixed $data The data to be stored in the cache.
     * @param int $expire The expiration time of the cache entry in seconds (default: 1800 seconds).
     * @return bool Returns true on success, false on failure.
     */
    private function _setCacheData($key, $data, $expire = 1800)
    {
        $cache = new Cache();
        return $cache->set($key, $data, $expire);
    }

    /**
     * Validates a raw query string to prevent full SQL statement execution.
     *
     * This function ensures the provided string only contains allowed expressions. 
     * It throws an exception if the string contains keywords associated with full SQL statements like `SELECT`, `INSERT`,
     * etc.
     *
     * @param string|array $string The raw query string to validate.
     * @param string $message (Optional) The exception message to throw (defaults to "Not supported to run full query").
     * @throws \InvalidArgumentException If the string contains forbidden keywords.
     */
    private function _forbidRawQuery($string, $message = 'Not supported to run full query')
    {
        $stringArr = is_string($string) ? [$string] : $string;

        $forbiddenKeywords = '/\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|TRUNCATE|REPLACE|GRANT|REVOKE|SHOW)\b/i';

        foreach ($stringArr as $str) {
            if (preg_match($forbiddenKeywords, $str)) {
                throw new \InvalidArgumentException($message);
            }
        }
    }

    /**
     * Binds parameters to a prepared statement.
     *
     * This function iterates through the provided bind values and binds them
     * to the prepared statement based on their data types. It supports positional
     * and named parameters, throwing exceptions for invalid key formats or query
     * structures. It also records the bound values for debugging purposes.
     *
     * @param \PDOStatement $stmt The prepared statement object.
     * @param array $binds An associative array of values to bind to the query.
     * @throws \PDOException If positional parameters use non-numeric keys or the query
     *                       format is invalid for placeholders.
     */
    private function _bindParams(\PDOStatement $stmt, array $binds)
    {
        $query = $stmt->queryString;

        // Check if the query contains positional or named parameters
        $hasPositional = strpos($query, '?') !== false;
        $hasNamed = preg_match('/:\w+/', $query);

        // Reset
        $this->_binds = [];
        $this->_profiler['profiling'][$this->_profilerActive]['binds'] = [];

        foreach ($binds as $key => $value) {

            $type = \PDO::PARAM_STR; // Default type to string

            if (is_int($value)) {
                $type = \PDO::PARAM_INT;
            } else if (is_bool($value)) {
                $type = \PDO::PARAM_BOOL;
            }

            if ($hasPositional) {
                // Positional parameter
                if (is_numeric($key)) {
                    $stmt->bindValue($key + 1, $value, $type);
                } else {
                    throw new \PDOException('Positional parameters require numeric keys', 400);
                }
            } else if ($hasNamed) {
                // Named parameter
                $stmt->bindValue(':' . $key, $value, $type);
            } else {
                throw new \PDOException('Query must contain either positional (?) or named (:number, :param) placeholders', 400);
            }

            $this->_binds[] = $value;
            $this->_profiler['profiling'][$this->_profilerActive]['binds'][] = $value; // Record only the value
        }
    }

    /**
     * Generates the full query string by replacing placeholders with bound values.
     *
     * This function analyzes the query string and bound parameters to determine
     * if they use positional or named placeholders. It then iterates through
     * the binds and replaces the corresponding placeholders in the query with
     * quoted values. It also sets the full query string in the profiler data.
     *
     * @param string $query The SQL query string with placeholders.
     * @param array $binds (optional) An associative array of values to bind to the query.
     * @throws \PDOException If positional parameters use non-numeric keys or the query
     *                       format is invalid for placeholders.
     * @return $this This object for method chaining.
     */
    private function _generateFullQuery($query, $binds = null)
    {
        if (!empty($binds)) {
            // Check if positional or named parameters are used
            $hasPositional = strpos($query, '?') !== false;
            $hasNamed = preg_match('/:\w+/', $query);

            foreach ($binds as $key => $value) {
                $quotedValue = is_numeric($value) ? $value : htmlspecialchars($value ?? '');

                if ($hasPositional) {
                    // Positional parameter: replace with quoted value
                    if (is_numeric($key)) {
                        $query = preg_replace('/\?/', $quotedValue, $query, 1);
                    } else {
                        throw new \PDOException('Positional parameters require numeric keys', 400);
                    }
                } else if ($hasNamed) {
                    // Named parameter: replace with quoted value
                    $query = str_replace(':' . $key, $quotedValue, $query);
                } else {
                    throw new \PDOException('Query must contain either positional (?) or named (:number, :param) placeholders', 400);
                }
            }
        }

        $this->_profiler['profiling'][$this->_profilerActive]['full_query'] = $query;

        return $this;
    }

    /**
     * Expands asterisks (*) in the SELECT clause to include all table columns.
     *
     * This function handles two scenarios:
     * 1. SELECT * FROM table: Replaces * with all columns from the table.
     * 2. SELECT fields FROM table: Adds .* to tables not already specified in fields.
     * It uses regular expressions to identify the query pattern and replace the asterisk
     * accordingly.
     *
     * @param string $query The SQL query string.
     * @return string The modified query string with expanded columns.
     */
    private function _expandAsterisksInQuery($query)
    {
        // Scenario 1: SELECT * FROM table
        if (preg_match('/SELECT\s+\*\s+FROM\s+([\w]+)/i', $query, $matches)) {
            $tables = [$matches[1]];

            // Add JOINed tables if present
            if (preg_match_all('/JOIN\s+([\w]+)\s+/i', $query, $joinMatches)) {
                $tables = array_merge($tables, $joinMatches[1]);
            }

            // Construct new SELECT part with table.*
            $selectPart = implode(', ', array_map(fn ($table) => "`$table`.*", $tables));
            $query = preg_replace('/SELECT\s+\*\s+FROM/i', "SELECT $selectPart FROM", $query, 1);
        } else if (preg_match('/SELECT\s+(.*)\s+FROM\s+([\w]+)/i', $query, $matches)) {
            // Scenario 2: SELECT fields FROM table
            $selectFields = $matches[1];
            $tables = [$matches[2]];

            // Add JOINed tables if present
            if (preg_match_all('/JOIN\s+([\w]+)\s+/i', $query, $joinMatches)) {
                $tables = array_merge($tables, $joinMatches[1]);
            }

            // Add .* only for tables not in select fields
            foreach ($tables as $table) {
                if (!preg_match("/\b$table\.\*/", $selectFields)) {
                    $selectFields .= ", $table.*";
                }
            }

            $query = preg_replace('/SELECT\s+(.*)\s+FROM/i', "SELECT $selectFields FROM", $query, 1);
        }

        return $query;
    }

    /**
     * Logs database errors and throws an exception.
     *
     * This function handles database errors by logging the error message and code,
     * and then throws a new exception with the details.
     *
     * @param \Exception $e The exception object representing the database error.
     * @param string $function (optional) The name of the function where the error occurred.
     * @param string $customMessage (optional) The description of error.
     * @throws \Exception A new exception with details from the database error.
     */
    private function db_error_log(\Exception $e, $function = '', $customMessage = 'Error executing')
    {
        try {
            // Log the error message and code
            $this->_error = [
                'code' => (int) $e->getCode(),
                'message' => "$customMessage '{$function}()': " . $e->getMessage(),
            ];

            log_message('error', "db->{$function}() : " . $e->getMessage());

            // Throw a new exception with formatted message and code
            throw new \Exception("$customMessage '{$function}()': " . $e->getMessage(), (int) $e->getCode());
        } catch (\Exception $e) {
            throw new \Exception('Database error occurred.', 0, $e);
        }
    }

    /**
     * Formats a number of bytes into a human-readable string with units.
     *
     * This function takes a number of bytes and converts it to a human-readable
     * format with appropriate units (B, KB, MB, GB, TB). It uses a specified
     * precision for rounding the value.
     *
     * @param int $bytes The number of bytes to format.
     * @param int $precision (optional) The number of decimal places to round to. Defaults to 2.
     * @return string The formatted string with units (e.g., 1023.45 KB).
     */
    private function _formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0); // Ensure non-negative bytes
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); // Calculate power of 1024
        $pow = min($pow, count($units) - 1); // Limit to valid unit index

        $bytes /= (1 << (10 * $pow)); // Divide by appropriate factor

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Retrieves the appropriate database function or full date string based on the database type.
     *
     * This function helps determine the correct function to use for date comparisons (YEAR, MONTH, DAY)
     * depending on the configured database type (mysql, mssql, oracle, firebird). It also allows requesting
     * a full date string by setting the `$getFullDate` parameter to true.
     *
     * @param string $column The name of the column (not used for full date string).
     * @throws \InvalidArgumentException If the database type is not supported.
     * @return mixed An associative array containing database functions (default) or a full date string.
     */
    private function _getDateFunction($column)
    {
        switch ($this->driver) {
            case 'mysql':
            case 'firebird':
                return [
                    "date" => "DATE_FORMAT($column, '%Y-%m-%d')",
                    "year" => "YEAR($column)",
                    "month" => "MONTH($column)",
                    "day" => "DAY($column)",
                ];
            case 'mssql':
                return [
                    "date" => "CONVERT(VARCHAR(10), $column, 126)",
                    "year" => "DATEPART(year, $column)",
                    "month" => "DATEPART(month, $column)",
                    "day" => "DATEPART(day, $column)",
                ];
            case 'oracle':
            case 'oci':
                return [
                    "date" => "TO_CHAR($column, 'YYYY-MM-DD')",
                    "year" => "EXTRACT(YEAR FROM $column)",
                    "month" => "EXTRACT(MONTH FROM $column)",
                    "day" => "EXTRACT(DAY FROM $column)",
                ];
            default:
                throw new \InvalidArgumentException("Unsupported database type: " . $this->driver);
        }
    }
}
