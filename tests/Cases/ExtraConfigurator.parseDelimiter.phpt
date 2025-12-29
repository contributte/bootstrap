<?php declare(strict_types = 1);

namespace Tests;

use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\Helpers;
use Tests\Fixtures\MockExtraConfigurator;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	$_SERVER = [];
	Helpers::env('NETTE.DATABASE.HOST', 'localhost');

	$configurator = new MockExtraConfigurator();
	$configurator->setParseDelimiter('.');
	$configurator->addEnvParameters();

	$configurator->setTempDirectory(Environment::getTestDir());
	$container = $configurator->createContainer();

	Assert::equal('localhost', $container->getParameters()['database']['host']);
});
