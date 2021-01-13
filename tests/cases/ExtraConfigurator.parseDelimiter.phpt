<?php declare(strict_types = 1);

namespace Tests;

use Ninjify\Nunjuck\Toolkit;
use Tester\Assert;
use Tests\Mocks\MockExtraConfigurator;

require_once __DIR__ . '/../bootstrap.php';

// Custom delimiter
Toolkit::test(function (): void {
	$_SERVER = [];
	env('NETTE.DATABASE.HOST', 'localhost');

	$configurator = new MockExtraConfigurator();
	$configurator->setParseDelimiter('.');
	$configurator->addEnvParameters();

	$configurator->setTempDirectory(TEMP_DIR);
	$container = $configurator->createContainer();

	Assert::equal('localhost', $container->getParameters()['database']['host']);
});
