<?php declare(strict_types = 1);

namespace Tests;

use Contributte\Bootstrap\PluggableConfigurator;
use Contributte\Tester\Notes;
use Contributte\Tester\Toolkit;
use Tester\Assert;
use Tests\Fixtures\MockContainerPlugin;
use Tests\Fixtures\MockDebugContainerPlugin;

require_once __DIR__ . '/../bootstrap.php';

Toolkit::test(function (): void {
	$pluggable = new PluggableConfigurator();
	$pluggable->setTempDirectory(TEMP_DIR);

	$pluggable->addPlugin(new MockContainerPlugin());
	$pluggable->addPlugin(new MockDebugContainerPlugin());

	$pluggable->setDebugMode(false);
	$pluggable->createContainer();

	Assert::equal([MockContainerPlugin::class], Notes::fetch());
});

Toolkit::test(function (): void {
	$pluggable = new PluggableConfigurator();
	$pluggable->setTempDirectory(TEMP_DIR);
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
