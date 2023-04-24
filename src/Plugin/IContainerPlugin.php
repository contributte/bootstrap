<?php declare(strict_types = 1);

namespace Contributte\Bootstrap\Plugin;

use Nette\Bootstrap\Configurator;
use Nette\DI\Container;

interface IContainerPlugin extends IPlugin
{

	public function plugin(Configurator $configurator, Container $container): void;

}
