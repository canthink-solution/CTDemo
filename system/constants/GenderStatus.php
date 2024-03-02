<?php

namespace Sys\constants;

final class GenderStatus
{
	public const MALE = 1;
	public const FEMALE = 2;

	public const LIST = [
		self::MALE => 'Male',
		self::FEMALE => 'Female',
	];

	public const WITH_ICON = [
		self::MALE => 'Male <i class="tf-icons ti ti-gender-male ti-xs" style="color:blue" title="Male"></i>',
		self::FEMALE => 'Female <i class="tf-icons ti ti-gender-female ti-xs" style="color:pink" title="Female"></i>',
	];

	public const ICON = [
		self::MALE => '<i class="tf-icons ti ti-gender-male ti-xs" style="color:blue" title="Male"></i>',
		self::FEMALE => '<i class="tf-icons ti ti-gender-female ti-xs" style="color:pink" title="Female"></i>',
	];
}
