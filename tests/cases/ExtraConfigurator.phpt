<?php declare(strict_types = 1);

namespace Tests;

/**
 * Test: ExtraConfigurator
 */

require_once __DIR__ . '/../bootstrap.php';

use Tester\Assert;
use Tests\Mocks\MockExtraConfigurator;

// Parsing NETTE__ parameters
test(function (): void {
	$_SERVER = [];
	env('NETTE__FOOBAR__BAR', 'foobar1');
	env('NETTE__FOOBAR__BAZ', 'foobar2');
	env('NETTE_INVALID', 'foobar3');

	$configurator = new MockExtraConfigurator();

	Assert::equal([
		'foobar' => [
			'bar' => 'foobar1',
			'baz' => 'foobar2',
		],
	], $configurator->getEnvironmentParameters());
});

// Parsing all ENV parameters
test(function (): void {
	$_SERVER = [];
	env('NETTE__FOOBAR__BAR', 'foobar1');
	env('NETTE_INVALID', 'foobar3');
	env('X', 'Y');

	$configurator = new MockExtraConfigurator();

	Assert::equal([
		'NETTE__FOOBAR__BAR' => 'foobar1',
		'NETTE_INVALID' => 'foobar3',
		'X' => 'Y',
	], $configurator->getAllEnvironmentParameters());
});

// ENV Debug mode
test(function (): void {
	$_SERVER = [];
	$configurator = new MockExtraConfigurator();

	env('NETTE_DEBUG', true);
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	env('NETTE_DEBUG', 'true');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	env('NETTE_DEBUG', '1');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	env('NETTE_DEBUG', false);
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	env('NETTE_DEBUG', 'false');
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	env('NETTE_DEBUG', '0');
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	env('NETTE_DEBUG', 'foobar');
	$configurator->setEnvDebugMode();
	Assert::false($configurator->isDebugMode());

	env('NETTE_DEBUG', '10.0.0.1');
	env('REMOTE_ADDR', '10.0.0.1');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());
});

// FILE Debug mode
test(function (): void {
	$configurator = new MockExtraConfigurator();
	$configurator->setDebugMode(false);
	$fileName = TEMP_DIR . '/.debug';

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
test(function (): void {
	$_SERVER = [];
	env('NETTE__DATABASE__HOST', 'localhost');

	$configurator = new MockExtraConfigurator();
	$configurator->addEnvParameters();
	$configurator->addParameters([
		'foobar' => '%database.host%',
	]);

	$configurator->setTempDirectory(TEMP_DIR);
	$container = $configurator->createContainer();

	Assert::equal('localhost', $container->getParameters()['database']['host']);
	Assert::equal('localhost', $container->getParameters()['foobar']);
});
