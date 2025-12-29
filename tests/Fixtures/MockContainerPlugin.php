<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Contributte\Bootstrap\Plugin\IContainerPlugin;
use Contributte\Tester\Utils\Notes;
use Nette\Bootstrap\Configurator;
use Nette\DI\Container;

final class MockContainerPlugin implements IContainerPlugin
{

	public function plugin(Configurator $configurator, Container $container): void
	{
		Notes::add(self::class);
	}

}
