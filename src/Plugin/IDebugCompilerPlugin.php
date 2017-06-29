<?php

namespace Contributte\Bootstrap\Plugin;

use Nette\Configurator;
use Nette\DI\Compiler;

interface IDebugCompilerPlugin extends IPlugin
{

	/**
	 * @param Configurator $configurator
	 * @param Compiler $compiler
	 * @return void
	 */
	public function plugin(Configurator $configurator, Compiler $compiler);

}
