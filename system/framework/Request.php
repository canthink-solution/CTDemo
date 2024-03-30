<?php

namespace Sys\framework;

/**
 * Request Class
 *
 * This class provides functionality for handling HTTP requests, including retrieving input data, managing files, and determining AJAX calls.
 *
 * @category  Utility
 * @package   HTTP Request Handling
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      https://example.com/documentation/request-class
 * @version   1.0.0
 */

class Request
{
    protected static $data; // Store request data

    /**
     * Constructor: Merge GET, POST, and FILES data into $data array
     */
    public function __construct()
    {
        self::$data = array_merge($_GET, $_POST, $_FILES);
        // self::$data = (object) array_merge($_GET, $_POST, $_FILES);
    }

    /**
     * Retrieve input data from the request
     *
     * @param string $key The key of the input data
     * @param mixed $default The default value if key does not exist
     * @return mixed The value of the input data
     */
    public static function input($key, $default = null)
    {
        // If no segments provided, just check if the data contains the key directly
        if (strpos($key, '.') === false) {
            return self::$data[$key] ?? $default;
        }

        // Split the key by dots to handle nested arrays
        $segments = explode('.', $key);
        $data = self::$data;

        foreach ($segments as $segment) {
            // If the segment is an asterisk, replace it with a regex wildcard
            if ($segment === '*') {
                $wildcardData = [];
                foreach ($data as $item) {
                    if (is_array($item)) {
                        $wildcardData = array_merge($wildcardData, $item);
                    }
                }
                $data = $wildcardData;
            } else if (isset($data[$segment])) {
                $data = $data[$segment]; // If the segment exists, go deeper
            } else {
                // If the segment doesn't exist, return the default value
                return $default;
            }
        }

        return $data ?? $default;
    }

    /**
     * Retrieve a GET request value
     *
     * @param string $key The key of the GET value
     * @param mixed $default The default value if key does not exist
     * @return mixed The value of the GET value
     */
    public static function get($key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Retrieve a POST request value
     *
     * @param string $key The key of the POST value
     * @param mixed $default The default value if key does not exist
     * @return mixed The value of the POST value
     */
    public static function post($key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Retrieve a file from the request
     *
     * @param string $key The key of the file
     * @param mixed $default The default value if key does not exist
     * @return mixed The file data
     */
    public static function file($key, $default = null)
    {
        return $_FILES[$key] ?? $default;
    }

    /**
     * Retrieve all of the input data for the request
     *
     * @return array All input data
     */
    public static function all()
    {
        return self::$data;
    }

    /**
     * Determine if the request contains a given input item
     *
     * @param string $key The key to check
     * @return bool True if the key exists, false otherwise
     */
    public static function has($key)
    {
        return isset(self::$data[$key]);
    }

    /**
     * Get a subset of the items from the input data
     *
     * @param array|string $keys The keys to retrieve
     * @return array The subset of input data
     * @throws InvalidArgumentException If $keys parameter is not an array or string
     */
    public static function only($keys)
    {
        if (!is_array($keys) && !is_string($keys)) {
            throw new InvalidArgumentException('Parameter $keys must be an array or a string.');
        }

        // Convert string keys to array
        $keys = is_array($keys) ? $keys : [$keys];

        return array_intersect_key(self::$data, array_flip($keys));
    }

    /**
     * Get all of the input except for a specified array of items
     *
     * @param array|string $keys The keys to exclude
     * @return array The input data excluding specified keys
     * @throws InvalidArgumentException If $keys parameter is not an array or string
     */
    public static function except($keys)
    {
        if (!is_array($keys) && !is_string($keys)) {
            throw new InvalidArgumentException('Parameter $keys must be an array or a string.');
        }

        // Convert string keys to array
        $keys = is_array($keys) ? $keys : [$keys];

        return array_diff_key(self::$data, array_flip($keys));
    }

    /**
     * Retrieve a header from the request
     *
     * @param string $key The header key
     * @param mixed $default The default value if header does not exist
     * @return mixed The header value
     */
    public static function header($key, $default = null)
    {
        return $_SERVER[$key] ?? $default;
    }

    /**
     * Check if the request has a specific header
     *
     * @param string $key The header key
     * @return bool True if header exists, false otherwise
     */
    public static function hasHeader($key)
    {
        return isset($_SERVER[$key]);
    }

    /**
     * Retrieve the request method
     *
     * @return string The request method (e.g., GET, POST, PUT, DELETE)
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Check if the request method matches a given method
     *
     * @param string $method The method to check against
     * @return bool True if the request method matches, false otherwise
     */
    public static function isMethod($method)
    {
        return strtoupper($method) === strtoupper($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Get the URL of the request
     *
     * @return string The request URL
     */
    public static function url()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the full URL of the request
     *
     * @return string The full request URL including scheme and host
     */
    public static function fullUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Retrieve the bearer token from the request
     *
     * @return string|null The bearer token if exists, null otherwise
     */
    public static function bearerToken()
    {
        $authorizationHeader = self::header('Authorization');
        if (strpos($authorizationHeader, 'Bearer ') === 0) {
            return substr($authorizationHeader, 7);
        }
        return null;
    }

    /**
     * Get the host of the request
     *
     * @return string The request host
     */
    public static function host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    /**
     * Get the path of the request
     *
     * @return string The request path
     */
    public static function path()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * Determine if the request is the result of an AJAX call
     *
     * @return bool True if it's an AJAX request, false otherwise
     */
    public static function ajax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
