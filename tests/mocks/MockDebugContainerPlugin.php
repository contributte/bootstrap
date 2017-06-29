<?php

namespace Tests\Mocks;

use Contributte\Bootstrap\Plugin\IDebugContainerPlugin;
use Nette\Configurator;
use Nette\DI\Container;
use Ninjify\Nunjuck\Notes;

final class MockDebugContainerPlugin implements IDebugContainerPlugin
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
