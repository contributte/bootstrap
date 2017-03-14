<?php

namespace Tests;

/**
 * Test: Configurator
 */

require_once __DIR__ . '/../bootstrap.php';

use Contributte\Bootstrap\Configurator;
use Tester\Assert;

final class MockConfigurator extends Configurator
{

	/**
	 * @return array
	 */
	public function getEnvironmentParameters()
	{
		return parent::getEnvironmentParameters();
	}

	/**
	 * @return array
	 */
	public function getDefaultParameters()
	{
		return parent::getDefaultParameters();
	}

}

test(function () {
	$_SERVER = [];
	$_SERVER['NETTE__FOOBAR__BAR'] = 'foobar1';
	$_SERVER['NETTE__FOOBAR__BAZ'] = 'foobar2';

	$configurator = new MockConfigurator();

	Assert::equal([
		'foobar' => [
			'bar' => 'foobar1',
			'baz' => 'foobar2',
		],
	], $configurator->getEnvironmentParameters());
});

test(function () {
	$_SERVER = [];
	$configurator = new MockConfigurator();

	$_SERVER['NETTE_DEBUG'] = TRUE;
	$configurator->autoDebugMode();
	Assert::true($configurator->isDebugMode());

	$_SERVER['NETTE_DEBUG'] = 'true';
	$configurator->autoDebugMode();
	Assert::true($configurator->isDebugMode());

	$_SERVER['NETTE_DEBUG'] = '1';
	$configurator->autoDebugMode();
	Assert::true($configurator->isDebugMode());

	$_SERVER['NETTE_DEBUG'] = 'false';
	$configurator->autoDebugMode();
	Assert::false($configurator->isDebugMode());

	$_SERVER['NETTE_DEBUG'] = '0';
	$configurator->autoDebugMode();
	Assert::false($configurator->isDebugMode());
});

test(function () {
	$_SERVER = [];
	$_SERVER['NETTE__DATABASE__HOST'] = 'localhost';

	$configurator = new MockConfigurator();
	$configurator->addParameters([
		'foobar' => '%database.host%',
	]);

	$configurator->setTempDirectory(TEMP_DIR);
	$container = $configurator->createContainer();

	Assert::equal('localhost', $container->getParameters()['database']['host']);
	Assert::equal('localhost', $container->getParameters()['foobar']);
});
