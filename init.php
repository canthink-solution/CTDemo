<?php

// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
	session_start(); // Start the session
}

/*
|--------------------------------------------------------------------------
| CHECK PHP INFO
|--------------------------------------------------------------------------
*/

if (isset($_GET['_ctphpinfo'])) {
  phpinfo();
  exit;
}

/*
|--------------------------------------------------------------------------
| SECTION A: CHECK PHP VERSION
|--------------------------------------------------------------------------
*/

// Check if PHP version is supported
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
  die('PHP version 8.0.0 or higher is required.');
}

/*
|--------------------------------------------------------------------------
| SECTION B: CONFIGURATION FOR ENVIRONMENT (development, staging, production)
|--------------------------------------------------------------------------
*/

// Define the environment
define('ENVIRONMENT', 'development');

// Set error reporting based on the environment
if (ENVIRONMENT === 'development') {
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
} else {
  error_reporting(0);
  ini_set('display_errors', 0);
}

// Define base URL
$urlProtocol = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
$appFolderPosition = strpos($_SERVER['SCRIPT_NAME'], 'app');
$basePath = $appFolderPosition !== false ? substr($_SERVER['SCRIPT_NAME'], 0, $appFolderPosition) : $_SERVER['SCRIPT_NAME'];
$basePath = str_replace('/index.php', '/', $basePath); // Remove index.php from the base path
$baseUrl = $urlProtocol . '://' . $_SERVER['HTTP_HOST'] . $basePath;
define('BASE_URL', $baseUrl);

// Define application name and directory
define('APP_NAME', "CT Demo"); // Replace to application name
define('APP_DIR', basename(BASE_URL) . '/app');

/*
|--------------------------------------------------------------------------
| SECTION C : CONFIGURE FOR TIMEZONE
|--------------------------------------------------------------------------
*/

// Set the default timezone to Asia/Kuala_Lumpur
define('APP_TIMEZONE', 'Asia/Kuala_Lumpur');
date_default_timezone_set(APP_TIMEZONE);

/*
|--------------------------------------------------------------------------
| SECTION D : AUTOLOAD - VENDOR
|--------------------------------------------------------------------------
*/

// Path to the autoload file
$autoloadPath = __DIR__ . '/vendor/autoload.php';

// Check if the autoload file exists
if (file_exists($autoloadPath)) {
  require_once $autoloadPath;
} else {
  die("Autoload file not found. Please run 'composer install' to install dependencies.");
}

/*
|--------------------------------------------------------------------------
| SECTION E : CORE SYSTEM & HELPERS
|--------------------------------------------------------------------------
*/

// Define directories to load files from
$directories = [
  "/app/config/*.php",
  "/system/helpers/*.php"
];

// Iterate over each directory
foreach ($directories as $directory) {
  // Get files matching the pattern
  $files = glob(__DIR__ . $directory);
  
  // Require each file
  foreach ($files as $file) {
      require_once $file;
  }
}

/*
|--------------------------------------------------------------------------
| SECTION F : REGISTER DATABASE (EXCEPT DEFAULT)
|--------------------------------------------------------------------------
*/

if (isset($config['db']) || is_array($config['db'])) {
  foreach ($config['db'] as $connectionName => $setting) {
    if ($connectionName === 'default') {
      continue; // Skip the default connection
    }

    // Check if the configuration for the current environment exists
    if (isset($setting[ENVIRONMENT]) && is_array($setting[ENVIRONMENT])) {
      // Check if the necessary parameters are set and not empty
      if (!empty($setting[ENVIRONMENT]['hostname']) && !empty($setting[ENVIRONMENT]['username'])) {
        db()->addConnection(
          trim($connectionName),
          [
            'driver' => $setting[ENVIRONMENT]['driver'] ?? 'mysql',
            'host' => $setting[ENVIRONMENT]['hostname'] ?? 'localhost',
            'username' => $setting[ENVIRONMENT]['username'] ?? 'root',
            'password' => $setting[ENVIRONMENT]['password'] ?? null,
            'db' => $setting[ENVIRONMENT]['database'] ?? null,
            'port' => $setting[ENVIRONMENT]['port'] ?? null,
            'charset' => $setting[ENVIRONMENT]['charset'] ?? null,
            'socket' => $setting[ENVIRONMENT]['socket'] ?? null
          ]
        );
      }
    }
  }
}

/*
|--------------------------------------------------------------------------
| SECTION G : LOAD FUNCTION IN CONTROLLER
|--------------------------------------------------------------------------
*/

if (isAjax()) {
  $action = request('action');
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action != 'modal') {
      if (hasData($action) && function_exists($action))
        call_user_func($action, $_REQUEST);
      else if (!hasData($action))
        dd("action does not define in callApi.");
      else if (!function_exists($action))
        dd("Function '$action' does not exist");
    }
  }
}

/*
|--------------------------------------------------------------------------
| SECTION H : LOAD MODAL (BOOTSTRAP) DYNAMIC
|--------------------------------------------------------------------------
*/

if (hasData($_POST, 'fileName')) {
  // Extract filename from POST request
  $filename = request('fileName');

  // Construct file path
  $filePath = $filename;

  // Check if file exists
  if (file_exists($filePath)) {
    // Extract data array from POST request
    $data = hasData($_POST, 'dataArray', true);

    // Configure options for HTTP request
    $opts = [
      'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => hasData($data) ? http_build_query($data) : null,
      ],
    ];

    // Create HTTP context
    $context = stream_context_create($opts);

    // Send HTTP POST request and output response
    echo file_get_contents($filePath, false, $context);
  } else {
    // If file doesn't exist, display error message
    echo '<div class="alert alert-danger" role="alert"> File <b><i>' . $filePath . '</i></b> does not exist. </div>';
  }
}

/*
|--------------------------------------------------------------------------
| SECTION I : MENU LIST
|--------------------------------------------------------------------------
*/

$menuList = [
  [
    'currentPage' => 'dashboard', // use in each file (without whitespace or any character)
    'desc' => 'Utama',
    'url' => 'app/views/dashboard',
    'icon' => 'tf-icons bx bxs-dashboard',
    'permission' => NULL,
  ],
  [
    'currentPage' => 'directory', // use in each file (without whitespace or any character)
    'desc' => 'Pengguna',
    'url' => 'app/views/directory',
    'icon' => 'tf-icons bx bx-user-circle',
    'permission' => 'directory-staff-view',
  ],
  [
    'currentPage' => 'settings', // use in each file (without whitespace or any character)
    'desc' => 'Tetapan',
    'url' => 'app/views/settings',
    'icon' => 'tf-icons bx bx-cog',
    'permission' => 'settings-view',
  ]
];

$redirectAuth = $menuList[0]['url'];
