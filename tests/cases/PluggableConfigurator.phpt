<?php

namespace Tests;

/**
 * Test: PluggableConfigurator
 */

require_once __DIR__ . '/../bootstrap.php';

use Contributte\Bootstrap\PluggableConfigurator;
use Ninjify\Nunjuck\Notes;
use Tester\Assert;
use Tests\Mocks\MockContainerPlugin;
use Tests\Mocks\MockDebugContainerPlugin;

test(function () {
	$pluggable = new PluggableConfigurator();
	$pluggable->setTempDirectory(TEMP_DIR);

	$pluggable->addPlugin(new MockContainerPlugin());
	$pluggable->addPlugin(new MockDebugContainerPlugin());

	$pluggable->setDebugMode(FALSE);
	$pluggable->createContainer();

	Assert::equal([MockContainerPlugin::class], Notes::fetch());
});

test(function () {
	$pluggable = new PluggableConfigurator();
	$pluggable->setTempDirectory(TEMP_DIR);
	unset($pluggable->defaultExtensions['di']);

	$pluggable->addPlugin(new MockContainerPlugin());
	$pluggable->addPlugin(new MockDebugContainerPlugin());

	$pluggable->setDebugMode(TRUE);
	$pluggable->createContainer();

	Assert::equal([
		MockContainerPlugin::class,
		MockDebugContainerPlugin::class,
	], Notes::fetch());
});
