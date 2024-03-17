<?php

/**
 * CSRF Protection Class
 * Provides methods to generate, retrieve, validate, and unset CSRF tokens.
 *
 * @category  Security
 * @package   Security
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   1.0.0
 */
class CSRF
{
    /**
     * @var string The name of the CSRF token used in forms.
     */
    private $tokenName = 'csrf_token';

    /**
     * CSRF constructor.
     * Starts the session if not already started.
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Retrieves the name of the CSRF token.
     *
     * @return string The CSRF token name.
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * Generates a CSRF token and stores it in session and a secure HTTP-only cookie.
     *
     * @return string The generated CSRF token.
     * @throws Exception If random bytes generation fails.
     */
    public function generateToken()
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[$this->tokenName] = $token;
        // Set cookie with Secure flag to true for HTTPS only
        $this->setTokenCookie($token);
        return $token;
    }

    /**
     * Retrieves the CSRF token from the session.
     *
     * @return string|null The CSRF token or null if not set.
     */
    public function getToken()
    {
        return $_SESSION[$this->tokenName] ?? null;
    }

    /**
     * Retrieves the CSRF token from the cookie.
     *
     * @return string|null The CSRF token or null if not set.
     */
    public function getTokenCookie()
    {
        return $_COOKIE[$this->tokenName] ?? null;
    }

    /**
     * Validates the provided token against the stored token.
     *
     * @param string $token The token to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    public function validateToken($token)
    {
        $sessionToken = $this->getToken();
        return ($sessionToken !== null && hash_equals($sessionToken, $token));
    }

    /**
     * Unsets the CSRF token from session and expires the CSRF cookie.
     */
    public function unsetToken()
    {
        unset($_SESSION[$this->tokenName]);
        setcookie($this->tokenName, '', time() - 3600, '/', '', true, true);  // Expire the CSRF cookie
    }

    /**
     * Sets the name of the CSRF token.
     *
     * @param string $tokenName The name of the CSRF token.
     */
    public function setTokenName($tokenName)
    {
        $this->tokenName = $tokenName;
    }

    /**
     * Sets the CSRF token cookie.
     *
     * @param string $token The CSRF token.
     */
    private function setTokenCookie($token)
    {
        setcookie($this->tokenName, $token, 0, '/', '', true, true);
    }
}
