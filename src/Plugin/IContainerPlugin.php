<?php

namespace Contributte\Bootstrap\Plugin;

use Nette\Configurator;
use Nette\DI\Container;

interface IContainerPlugin extends IPlugin
{

	/**
	 * @param Configurator $configurator
	 * @param Container $container
	 * @return void
	 */
	public function plugin(Configurator $configurator, Container $container);

}
