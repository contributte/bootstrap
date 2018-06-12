<?php declare(strict_types = 1);

namespace Tests\Mocks;

use Contributte\Bootstrap\Plugin\IContainerPlugin;
use Nette\Configurator;
use Nette\DI\Container;
use Ninjify\Nunjuck\Notes;

final class MockContainerPlugin implements IContainerPlugin
{

	public function plugin(Configurator $configurator, Container $container): void
	{
		Notes::add(__CLASS__);
	}

}
