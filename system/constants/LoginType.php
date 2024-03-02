<?php

namespace Sys\constants;

final class LoginType
{
	public const CREDENTIAL = 1;
	public const SOCIALITE = 2;
	public const TOKEN = 3;
	public const REMEMBER_ME = 4;

	public const LIST = [
		self::CREDENTIAL => 'Credential/Normal',
		self::SOCIALITE => 'Socialite',
		self::TOKEN => 'Token',
		self::REMEMBER_ME => 'REMEMBER ME TOKEN',
	];
}
