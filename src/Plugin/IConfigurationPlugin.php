<?php declare(strict_types = 1);

namespace Contributte\Bootstrap\Plugin;

use Nette\Configurator;

interface IConfigurationPlugin extends IPlugin
{

	public function plugin(Configurator $configurator): void;

}
