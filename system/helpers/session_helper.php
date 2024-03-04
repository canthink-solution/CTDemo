<?php

function isLogin($redirect = true, $param = 'isLogIn', $path = 'app/views/login')
{
	$isCurrentLogin = hasData($_SESSION, $param);
	if (!$isCurrentLogin && $redirect)
		redirect($path, true);

	return $isCurrentLogin;
}

function allSession($die = false)
{
	echo '<pre>';
	print_r($_SESSION);
	echo '</pre>';

	if ($die)
		die;
}

function startSession($param = NULL)
{
	foreach ($param as $sessionName => $sessionValue) {
		$_SESSION[$sessionName] = $sessionValue;
	}

	return $_SESSION;
}

function endSession($param = NULL)
{
	if (is_array($param)) {
		foreach ($param as $sessionName) {
			unset($_SESSION[$sessionName]);
		}
	} else {
		unset($_SESSION[$param]);
	}
}

function getSession($param = NULL)
{
	if (is_array($param)) {
		$sessiondata = [];
		foreach ($param as $sessionName) {
			array_push($sessiondata, $_SESSION[$sessionName]);
		}
		return $sessiondata;
	} else {
		return $_SESSION[$param] ?? '';
	}
}

// Permission

function hasPermission($slug = NULL)
{
	$sessionPermission = getSession('permission');

	if (!empty($sessionPermission)) {
		return empty($slug) ? true : in_array($slug, $sessionPermission) || in_array('*', $sessionPermission);
	}

	return empty($slug);
}

function hasAccessPage($slug = NULL, $redirect = false)
{
	$sessionPermission = getSession('permission');

	$checkAccess = false;
	if (!empty($sessionPermission)) {
		$checkAccess = empty($slug) ? true : in_array($slug, $sessionPermission) || in_array('*', $sessionPermission);
	}

	if (!empty($slug) && !$checkAccess) {
		if ($redirect) {
			error_page('403');
			exit;
		} else {
			die("No access to this page");
		}
	}

	return $checkAccess;
}


// Image Session

function isExistImage($path, $default = 'profile')
{
	$list = [
		'profile' => 'public' . DIRECTORY_SEPARATOR . 'general' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'user.png',
	];

	return !empty($path) && file_exists($path) ? $path : (array_key_exists($default, $list) ? $list[$default] : '');
}
