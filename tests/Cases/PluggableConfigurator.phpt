<?php declare(strict_types = 1);

namespace Tests;

use Contributte\Bootstrap\PluggableConfigurator;
use Contributte\Tester\Environment;
use Contributte\Tester\Toolkit;
use Contributte\Tester\Utils\Notes;
use Tester\Assert;
use Tests\Fixtures\MockContainerPlugin;
use Tests\Fixtures\MockDebugContainerPlugin;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	$pluggable = new PluggableConfigurator();
	$pluggable->setTempDirectory(Environment::getTestDir());

	$pluggable->addPlugin(new MockContainerPlugin());
	$pluggable->addPlugin(new MockDebugContainerPlugin());

	$pluggable->setDebugMode(false);
	$pluggable->createContainer();

	Assert::equal([MockContainerPlugin::class], Notes::fetch());
});

Toolkit::test(function (): void {
	$pluggable = new PluggableConfigurator();
	$pluggable->setTempDirectory(Environment::getTestDir());
	unset($pluggable->defaultExtensions['di']);

	$pluggable->addPlugin(new MockContainerPlugin());
	$pluggable->addPlugin(new MockDebugContainerPlugin());

	$pluggable->setDebugMode(true);
	$pluggable->createContainer();

	Assert::equal([
		MockContainerPlugin::class,
		MockDebugContainerPlugin::class,
	], Notes::fetch());
});
