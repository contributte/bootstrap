<?php declare(strict_types = 1);

namespace Tests;

use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\Helpers;
use Tests\Fixtures\MockExtraConfigurator;

require_once __DIR__ . '/../bootstrap.php';

// Parsing NETTE__ parameters
Toolkit::test(function (): void {
	$_SERVER = [];
	Helpers::env('NETTE__FOOBAR__BAR', 'foobar1');
	Helpers::env('NETTE__FOOBAR__BAZ', 'foobar2');
	Helpers::env('NETTE_INVALID', 'foobar3');

	$configurator = new MockExtraConfigurator();

	Assert::equal([
		'foobar' => [
			'bar' => 'foobar1',
			'baz' => 'foobar2',
		],
	], $configurator->getEnvironmentParameters());
});

// Parsing all ENV parameters
Toolkit::test(function (): void {
	$_SERVER = [];
	Helpers::env('NETTE__FOOBAR__BAR', 'foobar1');
	Helpers::env('NETTE_INVALID', 'foobar3');
	Helpers::env('X', 'Y');

	$configurator = new MockExtraConfigurator();

	Assert::equal([
		'NETTE__FOOBAR__BAR' => 'foobar1',
		'NETTE_INVALID' => 'foobar3',
		'X' => 'Y',
	], $configurator->getAllEnvironmentParameters());
});

// ENV Debug mode
Toolkit::test(function (): void {
	$_SERVER = [];
	$configurator = new MockExtraConfigurator();

	Helpers::env('NETTE_DEBUG', true);
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	Helpers::env('NETTE_DEBUG', 'true');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	Helpers::env('NETTE_DEBUG', '1');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	Helpers::env('NETTE_DEBUG', false);
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	Helpers::env('NETTE_DEBUG', 'false');
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	Helpers::env('NETTE_DEBUG', '0');
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	Helpers::env('NETTE_DEBUG', 'foobar');
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	Helpers::env('NETTE_DEBUG', '10.0.0.1');
	Helpers::env('REMOTE_ADDR', '10.0.0.1');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());
});

// FILE Debug mode
Toolkit::test(function (): void {
	$configurator = new MockExtraConfigurator();
	$configurator->setDebugMode(false);
	$fileName = Environment::getTestDir() . '/.debug';

	touch($fileName);
	$configurator->setFileDebugMode($fileName);
	Assert::true($configurator->isDebugMode());

	file_put_contents($fileName, 'false');
	$configurator->setFileDebugMode($fileName);
	Assert::false($configurator->isDebugMode());

	file_put_contents($fileName, 'true');
	$configurator->setFileDebugMode($fileName);
	Assert::true($configurator->isDebugMode());

	file_put_contents($fileName, 'FALSE');
	$configurator->setFileDebugMode($fileName);
	Assert::false($configurator->isDebugMode());

	file_put_contents($fileName, 'TRUE');
	$configurator->setFileDebugMode($fileName);
	Assert::true($configurator->isDebugMode());

	file_put_contents($fileName, '0');
	$configurator->setFileDebugMode($fileName);
	Assert::false($configurator->isDebugMode());

	file_put_contents($fileName, '1');
	$configurator->setFileDebugMode($fileName);
	Assert::true($configurator->isDebugMode());

	$configurator->setDebugMode(false);
	file_put_contents($fileName, '10.0.0.1');
	$configurator->setFileDebugMode($fileName);
	Assert::true($configurator->isDebugMode());
});

// Passing parameters to configurator
Toolkit::test(function (): void {
	$_SERVER = [];
	Helpers::env('NETTE__DATABASE__HOST', 'localhost');

	$configurator = new MockExtraConfigurator();
	$configurator->addEnvParameters();
	$configurator->addParameters([
		'foobar' => '%database.host%',
	]);

	$configurator->setTempDirectory(Environment::getTestDir());
	$container = $configurator->createContainer();

	Assert::equal('localhost', $container->getParameters()['database']['host']);
	Assert::equal('%database.host%', $container->getParameters()['foobar']);
});
