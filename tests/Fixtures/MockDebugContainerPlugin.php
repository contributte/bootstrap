<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Contributte\Bootstrap\Plugin\IDebugContainerPlugin;
use Contributte\Tester\Notes;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;

final class MockDebugContainerPlugin implements IDebugContainerPlugin
{

	public function plugin(Configurator $configurator, Container $container): void
	{
		Notes::add(self::class);
	}

}
