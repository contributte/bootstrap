<?php

namespace Contributte\Bootstrap\Plugin;

use Nette\Configurator;

interface IConfigurationPlugin extends IPlugin
{

	/**
	 * @param Configurator $configurator
	 * @return void
	 */
	public function plugin(Configurator $configurator);

}
