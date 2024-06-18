<?php

namespace Sys\framework;

/**
 * Cache Class
 *
 * This class handles caching functionality.
 *
 * @category  Cache
 * @package   Cache
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   0.1.0
 */

class Cache
{
    /**
     * @var string The path
     */
    private $path = '../../storage/';

    /**
     * @var string $cacheDir Directory to store cached data.
     */
    protected $cacheDir;

    /**
     * @var int $defaultExpire Default expiration time in seconds.
     */
    protected $defaultExpire;

    /**
     * Constructor.
     *
     * @param string $cacheDir Directory to store cached data. Defaults to 'cache'.
     * @param int $defaultExpire Default expiration time in seconds. Defaults to 3600 (1 hour).
     */
    public function __construct($cacheDir = 'cache', $defaultExpire = 3600)
    {
        $this->cacheDir = $this->path . $cacheDir;
        $this->defaultExpire = $defaultExpire;
    }

    /**
     * Get cached data for a key.
     *
     * @param string $key Cache key.
     * @return mixed Cached data or null if not found.
     */
    public function get($key)
    {
        $filename = $this->getCacheFilename($key);
        if (!file_exists($filename)) {
            return null;
        }

        // Check if cache expired
        if (time() - filemtime($filename) > $this->defaultExpire) {
            unlink($filename);
            return null;
        }

        // Read and return cached data
        return $this->decompressData(file_get_contents($filename));
    }

    /**
     * Set cached data for a key with expiration time.
     *
     * @param string $key Cache key.
     * @param mixed $data Data to be cached.
     * @param int $expire Expiration time in seconds. Defaults to defaultExpire.
     * @return bool True on success, false otherwise.
     */
    public function set($key, $data, $expire = null)
    {
        $filename = $this->getCacheFilename($key);
        
        if (empty($expire)) {
            $expire = $this->defaultExpire;
        }

        return file_put_contents($filename, $this->compressData($data), LOCK_EX) !== false;
    }

    /**
     * Delete cached data for a key.
     *
     * @param string $key Cache key.
     * @return bool True on success, false otherwise.
     */
    public function delete($key)
    {
        $filename = $this->getCacheFilename($key);
        return file_exists($filename) ? unlink($filename) : true;
    }

    /**
     * Generate cache filename based on key.
     *
     * @param string $key Cache key.
     * @return string Cache filename.
     */
    protected function getCacheFilename($key)
    {
        // Sanitize key and create filename
        $key = str_replace(['/', '\\'], '-', $key);
        $filename = $this->cacheDir . DIRECTORY_SEPARATOR . $key . '.cache';

        if (!file_exists($filename)) {
            $directory = dirname($filename);
            if (!file_exists($directory)) {
                mkdir($directory, 0775, true);
            }
            touch($filename);
            chmod($filename, 0775);
        }

        return $filename;
    }

    /**
     * Compresses data using gzip compression.
     *
     * @param mixed $data The data to compress.
     * @return string|false Returns the compressed data as a string, or false on failure.
     */
    protected function compressData($data)
    {
        return gzcompress(serialize($data), 9); // Using maximum compression level (9)
    }

    /**
     * Decompresses data compressed with gzip.
     *
     * @param string $compressedData The compressed data string.
     * @return mixed|false Returns the original data or false on failure.
     */
    protected function decompressData($compressedData)
    {
        return unserialize(gzuncompress($compressedData));
    }
}
