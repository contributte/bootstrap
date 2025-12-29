<?php declare(strict_types = 1);

namespace Tests\Fixtures;

final class Helpers
{

	public static function env(string $key, string|float|bool $value): void
	{
		$_SERVER[$key] = $value;
		putenv(sprintf('%s=%s', $key, $value));
	}

}
