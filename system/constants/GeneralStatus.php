<?php

namespace Sys\constants;

final class GeneralStatus
{
	public const INACTIVE = 0;
	public const ACTIVE = 1;
	public const SUSPENDED = 2;
	public const DELETED = 3;
	public const UNVERIFIED = 4;

	public const LIST = [
		self::ACTIVE => 'Active',
		self::INACTIVE => 'Inactive',
		self::SUSPENDED => 'Suspended',
		self::DELETED => 'Deleted',
		self::UNVERIFIED => 'Unverified',
	];

	public const BADGE = [
		self::ACTIVE => '<span class="badge badge-label bg-success"> Active </span>',
		self::INACTIVE => '<span class="badge badge-label bg-warning"> Inactive </span>',
		self::SUSPENDED => '<span class="badge badge-label bg-dark"> Suspended </span>',
		self::DELETED => '<span class="badge badge-label bg-danger"> Deleted </span>',
		self::UNVERIFIED => '<span class="badge badge-label bg-primary"> Unverified </span>',
	];

}
