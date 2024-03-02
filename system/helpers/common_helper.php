<?php

//  SECTION FOR INPUT RETURN

function request($param = NULL, $xssSecurity = true)
{
	$data = NULL;

	if (hasData($param)) {
		if (hasData($_GET, $param)) {
			$data = $_GET[$param];
		} else if (hasData($_POST, $param)) {
			$data = $_POST[$param];
		} else if (hasData($_REQUEST, $param)) {
			$data = $_REQUEST[$param];
		}
	}

	return $xssSecurity ? purify($data) : $data;
}

function json($data, $code = 200)
{
	header("Content-type:application/json");

	if (hasData($data, 'code'))
		$code = $data['code'];

	else if (hasData($data, 'resCode'))
		$code = $data['resCode'];

	http_response_code($code);
	echo json_encode($data, JSON_PRETTY_PRINT);
}

function response($code, $data = NULL, $returnType = 'array')
{
	http_response_code($code);

	if ($returnType == 'array')
		return $data;
	else
		json($data, JSON_PRETTY_PRINT);
}

// SECTION FOR GLOBAL DATE & TIME

/**
 * Get the current date in the specified format.
 *
 * @param string $format The format to use for the date. Default is 'Y-m-d'.
 * @return string The current date in the specified format.
 */
if (!function_exists('currentDate')) {
	function currentDate($format = 'Y-m-d')
	{
		setAppTimezone();
		return date($format);
	}
}

/**
 * Get the current time in the specified format.
 *
 * @param string $format The format to use for the time. Default is 'H:i:s'.
 * @return string The current time in the specified format.
 */
if (!function_exists('currentTime')) {
	function currentTime($format = 'H:i:s')
	{
		setAppTimezone();
		return date($format);
	}
}

/**
 * Format a given date string to the specified format.
 *
 * @param string $date The date string to format.
 * @param string $format The format to use for the date. Default is 'd.m.Y'.
 * @param mixed $defaultValue The value to return if the date is empty. Default is NULL.
 * @return string|null The formatted date string or the default value if the input date is empty.
 */
if (!function_exists('formatDate')) {
	function formatDate($date, $format = 'd.m.Y', $defaultValue = NULL)
	{
		setAppTimezone();
		return hasData($date) ? date($format, strtotime($date)) : $defaultValue;
	}
}

/**
 * Get the current timestamp in the specified format.
 *
 * @param string $format The format to use for the timestamp. Default is 'Y-m-d H:i:s'.
 * @return string The current timestamp in the specified format.
 */
if (!function_exists('timestamp')) {
	function timestamp($format = 'Y-m-d H:i:s')
	{
		setAppTimezone();
		return date($format);
	}
}

/**
 * Calculate the difference in days between two dates.
 *
 * @param string $d1 The first date.
 * @param string $d2 The second date.
 * @return int The difference in days between the two dates.
 */
if (!function_exists('dateDiff')) {
	function dateDiff($d1, $d2)
	{
		setAppTimezone();
		return round(abs(strtotime($d1) - strtotime($d2)) / 86400);
	}
}

/**
 * Calculate the difference in minutes between two times.
 *
 * @param string $t1 The first time.
 * @param string $t2 The second time.
 * @return int The difference in minutes between the two times.
 */
if (!function_exists('timeDiff')) {
	function timeDiff($t1, $t2)
	{
		setAppTimezone();
		return round(abs(strtotime($t1) - strtotime($t2)) / 60);
	}
}

/**
 * Set the default timezone to the application timezone specified in the environment.
 */
if (!function_exists('setAppTimezone')) {
	function setAppTimezone()
	{
		date_default_timezone_set(APP_TIMEZONE);
	}
}

// SECTION FOR CURRENCY & MONEY 

/**
 * Format a number as a money value with a specified number of decimals.
 *
 * @param float $amount The amount to format.
 * @param int $decimal The number of decimal places to include in the formatted amount (default is 2).
 * @return string The formatted amount as a string.
 */
if (!function_exists('money_format')) {
	function money_format($amount, $decimal = 2)
	{
		return number_format((float)$amount, $decimal, '.', ',');
	}
}

/**
 * Retrieve a mapping of currency codes to their respective locale settings.
 * This function returns an array where each currency code is associated with an array
 * containing symbol, pattern, code, and decimal settings for formatting the currency.
 * 
 * @return array An associative array where currency codes are keys and their locale settings are values.
 */
if (!function_exists('getCurrencyMapping')) {
	function getCurrencyMapping()
	{
		// Map the country codes to their respective locale codes
		return array(
			'USD' => ['symbol' => '$', 'pattern' => '$ #,##0.00', 'code' => 'en_US', 'decimal' => 2], // United States Dollar (USD)
			'JPY' => ['symbol' => '¥', 'pattern' => '¥ #,##0', 'code' => 'ja_JP', 'decimal' => 2], // Japanese Yen (JPY)
			'GBP' => ['symbol' => '£', 'pattern' => '£ #,##0.00', 'code' => 'en_GB', 'decimal' => 2], // British Pound Sterling (GBP)
			'EUR' => ['symbol' => '€', 'pattern' => '€ #,##0.00', 'code' => 'en_GB', 'decimal' => 2], // Euro (EUR) - Using en_GB for Euro
			'AUD' => ['symbol' => 'A$', 'pattern' => 'A$ #,##0.00', 'code' => 'en_AU', 'decimal' => 2], // Australian Dollar (AUD)
			'CAD' => ['symbol' => 'C$', 'pattern' => 'C$ #,##0.00', 'code' => 'en_CA', 'decimal' => 2], // Canadian Dollar (CAD)
			'CHF' => ['symbol' => 'CHF', 'pattern' => 'CHF #,##0.00', 'code' => 'de_CH', 'decimal' => 2], // Swiss Franc (CHF)
			'CNY' => ['symbol' => '¥', 'pattern' => '¥ #,##0.00', 'code' => 'zh_CN', 'decimal' => 2], // Chinese Yuan (CNY)
			'SEK' => ['symbol' => 'kr', 'pattern' => 'kr #,##0.00', 'code' => 'sv_SE', 'decimal' => 2], // Swedish Krona (SEK)
			'MYR' => ['symbol' => 'RM', 'pattern' => 'RM #,##0.00', 'code' => 'ms_MY', 'decimal' => 2], // Malaysian Ringgit (MYR)
			'SGD' => ['symbol' => 'S$', 'pattern' => 'S$ #,##0.00', 'code' => 'en_SG', 'decimal' => 2], // Singapore Dollar (SGD)
			'INR' => ['symbol' => '₹', 'pattern' => '₹ #,##0.00', 'code' => 'en_IN', 'decimal' => 2], // Indian Rupee (INR)
			'IDR' => ['symbol' => 'Rp', 'pattern' => 'Rp #,##0', 'code' => 'id_ID', 'decimal' => 0], // Indonesian Rupiah (IDR)
		);
	}
}

/**
 * Retrieve the currency symbol for a given currency code.
 *
 * This function checks if the provided currency code exists in a currency mapping
 * and returns the corresponding currency symbol. If the currency code is not found,
 * it returns an error message indicating an invalid country code.
 *
 * @param string|null $currencyCode The currency code for which to retrieve the symbol.
 * @return string The currency symbol or an error message if the code is invalid.
 */
if (!function_exists('currencySymbol')) {
	function currencySymbol($currencyCode = 'MYR')
	{
		$localeMap = getCurrencyMapping();

		if (!array_key_exists($currencyCode, $localeMap)) {
			return "Error: Invalid country code.";
		}

		return $localeMap[$currencyCode]['symbol'];
	}
}

/**
 * Format a given numeric value into a localized currency representation using the "intl" extension.
 *
 * @param float $value The numeric value to format as currency.
 * @param string|null $code (Optional) The country code to determine the currency format (e.g., 'USD', 'EUR', 'JPY', etc.).
 * @param bool $includeSymbol (Optional) Whether to include the currency symbol in the formatted output (default is false).
 * @return string The formatted currency value as a string or an error message if the "intl" extension is not installed or enabled.
 */
if (!function_exists('formatCurrency')) {
	function formatCurrency($value, $code = 'MYR', $includeSymbol = false)
	{
		// Check if the "intl" extension is installed and enabled
		if (!extension_loaded('intl')) {
			return 'Error: The "intl" extension is not installed or enabled, which is required for number formatting.';
		}

		if (empty($value)) {
			$value = 0.0;
		}

		// Map the country codes to their respective locale codes
		$localeMap = getCurrencyMapping();

		if (!array_key_exists($code, $localeMap)) {
			return "Error: Invalid country code.";
		}

		// Validate the $includeSymbol parameter
		if (!is_bool($includeSymbol)) {
			return "Error: \$includeSymbol parameter must be a boolean value.";
		}

		$currencyData = $localeMap[$code];

		// Create a NumberFormatter instance with the desired locale (country code)
		$formatter = new NumberFormatter($currencyData['code'], NumberFormatter::DECIMAL);
		$formatter->setAttribute(NumberFormatter::FRACTION_DIGITS, $currencyData['decimal']); // Set fraction digits

		if ($includeSymbol) {
			$formatter->setPattern($currencyData['pattern']);
		}

		// Format the currency value using the NumberFormatter
		return $formatter->formatCurrency($value, $currencyData['code']);
	}
}


// SECTION FOR GLOBAL FUNCTION

function redirect($path, $permanent = false)
{
	header('Location: ' . url($path), true, $permanent ? 301 : 302);
	exit();
}

function asset($param, $public = TRUE)
{
	// Check if $param already contains 'public'
	if ($public && strpos($param, 'public/') !== 0) {
		$param = 'public/' . $param;
	}

	return BASE_URL . $param;
}

function url($param)
{
	$param = htmlspecialchars($param, ENT_NOQUOTES, 'UTF-8');
	return BASE_URL . filter_var($param, FILTER_SANITIZE_URL);
}

function encode_base64($sData)
{
	$sBase64 = base64_encode($sData);
	return strtr($sBase64, '+/', '-_');
}

function decode_base64($sData)
{
	$sBase64 = strtr($sData, '-_', '+/');
	return base64_decode($sBase64);
}

function encodeID($id, $count = 15)
{
	$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$uniqueURL = substr(str_shuffle($permitted_chars), 0, $count) . '' . $id . '' . substr(str_shuffle($permitted_chars), 0, $count);
	return encode_base64($uniqueURL);
}

function decodeID($id, $count = 15)
{
	$id = decode_base64($id);
	return substr($id, $count, -$count);
}

function genRunningNo($currentNo, $prefix = NULL, $suffix = NULL, $separator = NULL, $leadingZero = 1)
{
	$nextNo = (int) $currentNo + 1;

	$pref = empty($separator) ? $prefix : $prefix . $separator;
	$suf = !empty($suffix) ? (empty($separator) ? $suffix : $separator . $suffix) : NULL;

	return [
		'code' => $pref . str_pad($nextNo, $leadingZero, 0, STR_PAD_LEFT) . $suf,
		'next' => $nextNo
	];
}

function truncateText($string, $length, $suffix = '...')
{
	$truncated = NULL;

	if (hasData($string)) {
		// If the string is shorter than or equal to the maximum length, return the string as is
		if (strlen($string) <= $length) {
			return $string;
		}

		// Truncate the string to the specified length
		$truncated = substr($string, 0, $length);

		// If the truncated string ends with a space, remove the space
		if (substr($truncated, -1) == ' ') {
			$truncated = substr($truncated, 0, -1);
		}

		// Append the suffix to the truncated string
		$truncated .= $suffix;
	}

	return $truncated;
}

function isAjax()
{
	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		return true;
	} else {
		return false;
	}
}

function isSuccess($response = NULL)
{
	$successStatus = [200, 201, 302];

	if (hasData($response)) {
		// check if array
		if (is_array($response))
			$response = hasData($response, 'resCode') ? $response['resCode'] : (hasData($response, 'code') ? $response['code'] : NULL);

		$code = (is_string($response)) ? (int)$response : $response;

		if (in_array($code, $successStatus)) {
			return true;
		}
	}

	return false;
}

function isError($response = NULL)
{
	$errorStatus = [400, 403, 404, 422, 500];

	if (hasData($response)) {
		// check if array
		if (is_array($response))
			$response = hasData($response, 'resCode') ? $response['resCode'] : (hasData($response, 'code') ? $response['code'] : NULL);

		$code = (is_string($response)) ? (int)$response : $response;

		if (in_array($code, $errorStatus)) {
			return true;
		}
	}

	return false;
}

/**
 * Check if the provided data contains non-empty values for the specified key.
 *
 * @param mixed       $data          The data to be checked (array or string).
 * @param string|null $arrKey        The key to check within the data.
 * @param bool        $returnData    If true, returns the data value if found.
 * @param mixed       $defaultValue  The default value to return if data is not found.
 *
 * @return bool|string|null Returns true if data exists, data value if $returnData is true and data exists, otherwise null or $defaultValue.
 */
if (!function_exists('hasData')) {
	function hasData($data = NULL, $arrKey = NULL, $returnData = false, $defaultValue = NULL)
	{
		// Base case 1: Check if data is not set, empty, or null
		if (!isset($data) || empty($data) || is_null($data)) {
			return $returnData ? ($defaultValue ?? $data) : false;
		}

		// Base case 2: If arrKey is not provided, consider data itself as having data
		if (is_null($arrKey)) {
			return $returnData ? ($defaultValue ?? $data) : true;
		}

		// Replace square brackets with dots in arrKey
		$arrKey = str_replace(['[', ']'], ['.', ''], $arrKey);

		// Split the keys into an array
		$keys = explode('.', $arrKey);

		// Helper function to recursively traverse the data
		$traverse = function ($keys, $currentData) use (&$traverse, $returnData, $defaultValue) {
			if (empty($keys)) {
				return $returnData ? $currentData : true;
			}

			$key = array_shift($keys);

			// Check if $currentData is an array or an object
			if (is_array($currentData) && array_key_exists($key, $currentData)) {
				return $traverse($keys, $currentData[$key]);
			} elseif (is_object($currentData) && isset($currentData->$key)) {
				return $traverse($keys, $currentData->$key);
			} else {
				// If the key doesn't exist, return the default value or false
				return $returnData ? $defaultValue : false;
			}
		};

		return $traverse($keys, $data);
	}
}

// SECTION FOR ARRAY

function isAssociative($arr)
{
	foreach (array_keys($arr) as $key)
		if (!is_int($key)) return TRUE;
	return FALSE;
}

function isMultidimension($arr)
{
	if (!empty($arr)) {
		rsort($arr);
		return isset($arr[0]) && is_array($arr[0]);
	} else {
		return $arr;
	}
}

// SECTION FOR NO DATA ERROR

function nodata($showText = true, $filesName = '5.png')
{
	echo "<div id='nodata' class='col-lg-12 mb-4 mt-3'>
	  <center>
		<img src='" . asset('general/images/nodata/' . $filesName) . "' class='img-fluid mb-3' width='38%'>
		<h4 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;margin-bottom:15px'> 
		 <strong> NO INFORMATION FOUND </strong>
		</h4>";
	if ($showText) {
		echo "<h6 style='letter-spacing :2px; font-family: Quicksand, sans-serif !important;font-size: 13px;'> 
			Here are some action suggestions for you to try :- 
		</h6>";
	}
	echo "</center>";
	if ($showText) {
		echo "<div class='row d-flex justify-content-center w-100'>
		<div class='col-lg m-1 text-left' style='max-width: 350px !important;letter-spacing :1px; font-family: Quicksand, sans-serif !important;font-size: 12px;'>
		  1. Try the registrar function (if any).<br>
		  2. Change your word or search selection.<br>
		  3. Contact the system support immediately.<br>
		</div>
	  </div>";
	}
	echo "</div>";
}


// SECTION FOR ERROR

function error_page($code, $path = 'app/views/errors/')
{
    $errorFile = $path . $code . '.php';
	redirect($errorFile, true);
}