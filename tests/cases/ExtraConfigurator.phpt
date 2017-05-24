<?php

namespace Tests;

/**
 * Test: ExtraConfigurator
 */

require_once __DIR__ . '/../bootstrap.php';

use Contributte\Bootstrap\ExtraConfigurator;
use Tester\Assert;

final class MockExtraConfigurator extends ExtraConfigurator
{

}

// Parsing NETTE__ parameters
test(function () {
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
test(function () {
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

// Debug mode
test(function () {
	$_SERVER = [];
	$configurator = new MockExtraConfigurator();

	env('NETTE_DEBUG', TRUE);
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	env('NETTE_DEBUG', 'true');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	env('NETTE_DEBUG', '1');
	$configurator->setEnvDebugMode();
	Assert::true($configurator->isDebugMode());

	env('NETTE_DEBUG', FALSE);
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

// Passing parameters to configurator
test(function () {
	$_SERVER = [];
	env('NETTE__DATABASE__HOST', 'localhost');

	$configurator = new MockExtraConfigurator();
	$configurator->addParameters([
		'foobar' => '%database.host%',
	]);

	$configurator->setTempDirectory(TEMP_DIR);
	$container = $configurator->createContainer();

	Assert::equal('localhost', $container->getParameters()['database']['host']);
	Assert::equal('localhost', $container->getParameters()['foobar']);
});
