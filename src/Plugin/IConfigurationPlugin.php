<?php declare(strict_types = 1);

namespace Contributte\Bootstrap\Plugin;

use Nette\Bootstrap\Configurator;

interface IConfigurationPlugin extends IPlugin
{

	public function plugin(Configurator $configurator): void;

}
