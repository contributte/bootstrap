<?php

namespace Tests\Mocks;

use Contributte\Bootstrap\Plugin\IContainerPlugin;
use Nette\Configurator;
use Nette\DI\Container;
use Ninjify\Nunjuck\Notes;

final class MockContainerPlugin implements IContainerPlugin
{

	/**
	 * @param Configurator $configurator
	 * @param Container $container
	 * @return void
	 */
	public function plugin(Configurator $configurator, Container $container)
	{
		Notes::add(__CLASS__);
	}

}
