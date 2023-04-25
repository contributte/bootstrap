<?php declare(strict_types = 1);

namespace Tests;

use Contributte\Bootstrap\ExtraConfigurator;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	Assert::equal(ExtraConfigurator::deepParameters([], [], 'felix'), []);

	$db = [];
	$db = ExtraConfigurator::deepParameters($db, ['database', 'user'], 'felix');
	$db = ExtraConfigurator::deepParameters($db, ['database', 'password'], 'contributte');
	Assert::equal($db, ['database' => ['user' => 'felix', 'password' => 'contributte']]);
});
