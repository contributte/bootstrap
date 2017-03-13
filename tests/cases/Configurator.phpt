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
	$_SERVER['NETTE__FOOBAR'] = 'foobar1';
	$_SERVER['NETTE__FOOBAR__FOOBAR'] = 'foobar2';
	$configurator = new MockConfigurator();
	Assert::equal([
		'foobar' => 'foobar1',
		'foobar.foobar' => 'foobar2',
	], $configurator->getEnvironmentParameters());
});

test(function () {
	$_SERVER['NETTE_DEBUG'] = TRUE;
	$configurator = new MockConfigurator();
	$configurator->autoDebugMode();
	Assert::true($configurator->isDebugMode());
});
