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
 * @version   0.1.1
 */

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
        if (!in_array($driver, ['mysql', 'mssql', 'oracle', 'firebird'])) {
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
        $this->_profiler['binds'] = []; // Initialize the binds array
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
                    $query = "SELECT table_name FROM user_tables WHERE table_name = '$table'";
                    break;
                case 'firebird':
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
        $this->orderBy = null;
        $this->groupBy = null;
        $this->where = null;
        $this->joins = null;
        $this->_error = [];
        $this->_secure = true;
        $this->_binds = [];
        $this->_query = [];

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

        $this->limit = $limit;
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
            // Validate input type
            if (!is_string($columnName) && !is_array($columnName)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string or an associative array.');
            }

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
            // Validate input type
            if (!is_string($columnName) && !is_array($columnName)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string or an associative array.');
            }

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
            // Validate column name type
            if (!is_string($columnName)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
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
                    throw new \InvalidArgumentException('Invalid start or end value for NOT BETWEEN. Must be numeric, date (YYYY-MM-DD), or time (HH:MM:SS).');
                }
            }

            // Ensure start is less than or equal to end for valid range
            if (!($formattedValues[0] <= $formattedValues[1])) {
                throw new \InvalidArgumentException('Start value must be less than or equal to end value for NOT BETWEEN.');
            }

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
            // Validate column name type
            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            if (!is_array($value)) {
                throw new \InvalidArgumentException("Value for 'IN' operator must be an array");
            }

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
            // Validate column name type
            if (!is_string($column)) {
                throw new \InvalidArgumentException('Invalid column name. Must be a string.');
            }

            if (!is_array($value)) {
                throw new \InvalidArgumentException("Value for 'NOT IN' operator must be an array");
            }

            $this->_buildWhereClause($column, $value, 'NOT IN', $whereType);
            return $this;
        } catch (\InvalidArgumentException $e) {
            $this->db_error_log($e, __FUNCTION__);
        }
    }

    /**
     * Performs a join operation on the current query.
     *
     * This function allows you to join another table to the current query
     * based on specified columns and a join type.
     *
     * @param string $tableToJoin The name of the table to join.
     * @param string $columnInTableJoin The column name in the joined table.
     * @param string $columnInTableRefer The column name in the current table for reference.
     * @param string $joinType (optional) The type of join (e.g., 'LEFT', 'RIGHT', 'INNER'). Defaults to 'LEFT'.
     * @throws \InvalidArgumentException If any of the column names or join type are invalid.
     * @return $this This object for method chaining.
     */
    public function join($tableToJoin, $columnInTableJoin, $columnInTableRefer, $joinType = 'LEFT')
    {
        // Validate input parameters
        if (!is_string($tableToJoin) || !is_string($columnInTableJoin) || !is_string($columnInTableRefer)) {
            throw new \InvalidArgumentException('Invalid column names or table name provided.');
        }

        $validJoinTypes = ['LEFT', 'RIGHT', 'INNER'];
        if (!in_array(strtoupper($joinType), $validJoinTypes)) {
            throw new \InvalidArgumentException('Invalid join type. Valid types are: ' . implode(', ', $validJoinTypes));
        }

        if (empty($this->table)) {
            throw new \Exception('No table selected', 400);
        }

        // Build the join clause
        $this->joins .= " $joinType JOIN `$tableToJoin` ON `$tableToJoin`.`$columnInTableJoin` = `$this->table`.`$columnInTableRefer`";

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
    private function _buildWhereClause($columnName, $value, $operator = '=', $whereType = 'AND')
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
                $this->_binds = array_merge($this->_binds, $value);
                break;
            case 'BETWEEN':
            case 'NOT BETWEEN':
                if (!is_array($value) || count($value) !== 2) {
                    throw new \InvalidArgumentException("Value for 'BETWEEN' or 'NOT BETWEEN' operator must be an array with two elements (start and end)");
                }
                $this->where .= "($columnName $operator $placeholder AND $placeholder)";
                $this->_binds = array_merge($this->_binds, $value);
                break;
            default:
                $this->where .= "$columnName $operator $placeholder";
                $this->_binds[] = $value;
        }
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
        // Build the final SELECT query string
        $this->_buildSelectQuery();

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
            $this->_profiler['query'] = $this->_query;

            // Generate the full query string with bound values 
            $this->_generateFullQuery($this->_query, $this->_binds);

            // Execute the prepared statement
            $stmt->execute();

            // Fetch all results as associative arrays
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Process eager loading if implemented 
            // $result = $this->_processEagerLoading($result);

        } catch (\PDOException $e) {
            // Log database errors
            $this->db_error_log($e, __FUNCTION__);
            throw $e; // Re-throw the exception
        }

        // Stop profiler 
        $this->_stopProfiler();

        // Reset internal properties for next query
        $this->reset();

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
        // Set limit to 1 to ensure only 1 data return
        $this->limit(1);

        // Build the final SELECT query string
        $this->_buildSelectQuery();

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
            $this->_profiler['query'] = $this->_query;

            // Generate the full query string with bound values
            $this->_generateFullQuery($this->_query, $this->_binds);

            // Execute the prepared statement
            $stmt->execute();

            // Fetch only the first result as an associative array
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Process eager loading if implemented 
            // $result = $this->_processEagerLoading($result);

        } catch (\PDOException $e) {
            // Log database errors
            $this->db_error_log($e, __FUNCTION__);
            throw $e; // Re-throw the exception
        }

        // Stop profiler
        $this->_stopProfiler();

        // Reset internal properties for next query
        $this->reset();

        // Return the first result or null if not found
        return $result;
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
            $this->_query .= " ORDER BY " . $this->orderBy;
        }

        // Add LIMIT clause if specified
        if ($this->limit) {
            $this->_query .= " LIMIT " . $this->limit;
        }

        // Expand asterisks in the query (replace with actual column names)
        $this->_query = $this->_expandAsterisksInQuery($this->_query);

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
    public function orderBy($columns, string $direction = 'ASC')
    {
        // Check if direction is valid
        if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException('Order direction must be "ASC" or "DESC".');
        }

        if (is_array($columns)) {
            $orderBy = [];
            foreach ($columns as $column => $dir) {
                $direction = strtoupper(!in_array(strtoupper($dir), ['ASC', 'DESC']) ? 'DESC' : $dir); // Set default to DESC if is not match 
                $orderBy[] = "$column $direction";
            }
            $this->orderBy = implode(', ', $orderBy);
        } else {
            $this->orderBy = "$columns $direction";
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
     * Sanitize input data to prevent XSS and SQL injection attacks based on the secure flag.
     *
     * @param mixed $value The input data to sanitize.
     * @return mixed|null The sanitized input data or null if $value is null or empty.
     */
    protected function sanitize($value = null)
    {
        // Check if $value is not null or empty
        if (!isset($value) || is_null($value)) {
            return $value;
        }

        // Check if secure mode is enabled
        if ($this->_secure) {
            // Sanitize input to prevent XSS
            if (is_array($value)) {
                // Sanitize each value in the array
                foreach ($value as &$val) {
                    // Check if $val is not null and not empty, and not equal to 0
                    if (!is_null($val) && !empty($val) && !is_integer($val)) {
                        $val = htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8'); // Apply XSS protection to $val
                    }
                }
                return $value;
            } else {
                // Sanitize a single value
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            }
        } else {
            // Return input as-is if secure mode is disabled
            return $value;
        }
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
        $this->_profiler = [
            'method' => $method,
            'start' => $startTime,
            'start_formatted' => date('Y-m-d H:i:s', (int) $startTime) . sprintf(".%02d", ($startTime - (int)$startTime) * 100),
            'query' => null, // Placeholder for query string
            'binds' => null,  // Placeholder for bound parameters
            'end' => null,    // Placeholder for end time
            'end_formatted' => null,  // Placeholder for formatted end time
            'execution_time' => null,  // Placeholder for execution time
            'status' => null,        // Placeholder for execution status
        ];
    }

    /**
     * Stops the profiler and calculates execution time and status.
     *
     * This function is called after query execution. It calculates the execution
     * time, formats it, and sets the execution status based on predefined thresholds.
     * It also updates the profiler data with end time, formatted end time, execution time, and status.
     */
    private function _stopProfiler()
    {
        $endTime = microtime(true);
        $executionTime = $endTime - $this->_profiler['start'];

        $this->_profiler['end'] = $endTime;
        $this->_profiler['end_formatted'] = date('Y-m-d H:i:s', (int) $endTime) . sprintf(".%02d", ($endTime - (int) $endTime) * 100);

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

        $this->_profiler['execution_time'] = $formattedExecutionTime;

        // Set execution status based on predefined thresholds
        $this->_profiler['status'] = ($executionTime > 4) ? 'very slow' : (($executionTime > 1.5 && $executionTime <= 3.59) ? 'slow' : (($executionTime > 0.5 && $executionTime <= 1.49) ? 'fast' : 'very fast'));
    }

    # HELPER SECTION

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
            $this->_profiler['binds'][] = $value; // Record only the value
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
                $quotedValue = is_numeric($value) ? $value : $this->pdo[$this->connectionName]->quote($value);

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

        $this->_profiler['full_query'] = $query;

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
}
