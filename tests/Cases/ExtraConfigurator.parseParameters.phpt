<?php declare(strict_types = 1);

namespace Tests;

use Contributte\Bootstrap\ExtraConfigurator;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\Helpers;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	Helpers::env('NETTE__TOKEN', '123456');
	Assert::equal(ExtraConfigurator::parseParameters($_SERVER, 'NETTE__'), ['token' => '123456']);
});
