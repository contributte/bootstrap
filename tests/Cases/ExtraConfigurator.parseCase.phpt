<?php declare(strict_types = 1);

namespace Tests;

use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\MockExtraConfigurator;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	$_SERVER = [];
	env('NETTE__USer', 'felix');

	// LOWERCASE
	$configurator = new MockExtraConfigurator();
	$configurator->addEnvParameters();
	$configurator->setTempDirectory(TEMP_DIR);
	$container = $configurator->createContainer();
	Assert::equal('felix', $container->getParameters()['user']);

	// UPPERCASE
	$configurator->setParseCase($configurator::PARSE_UPPERCASE);
	$configurator->addEnvParameters();
	$configurator->setTempDirectory(TEMP_DIR);
	$container = $configurator->createContainer();
	Assert::equal('felix', $container->getParameters()['USER']);

	// NATURAL
	$configurator->setParseCase($configurator::PARSE_NATURAL);
	$configurator->addEnvParameters();
	$configurator->setTempDirectory(TEMP_DIR);
	$container = $configurator->createContainer();
	Assert::equal('felix', $container->getParameters()['USer']);
});
