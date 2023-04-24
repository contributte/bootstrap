<?php declare(strict_types = 1);

namespace Contributte\Bootstrap\Plugin;

use Nette\Bootstrap\Configurator;
use Nette\DI\Compiler;

interface ICompilerPlugin extends IPlugin
{

	public function plugin(Configurator $configurator, Compiler $compiler): void;

}
