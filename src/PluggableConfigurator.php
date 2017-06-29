<?php

namespace Contributte\Bootstrap;

use Contributte\Bootstrap\Plugin\ICompilerPlugin;
use Contributte\Bootstrap\Plugin\IContainerPlugin;
use Contributte\Bootstrap\Plugin\IDebugCompilerPlugin;
use Contributte\Bootstrap\Plugin\IDebugContainerPlugin;
use Contributte\Bootstrap\Plugin\IPlugin;
use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\Container;

class PluggableConfigurator extends Configurator
{

	/** @var IPlugin[] */
	protected $plugins = [];

	/**
	 * Creates configurator
	 */
	public function __construct()
	{
		parent::__construct();

		// Attach compiler plugin
		$this->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
			$this->compile($configurator, $compiler);
		};
	}

	/**
	 * GETTERS/SETTERS *********************************************************
	 */

	/**
	 * Collect default parameters
	 *
	 * @return array
	 */
	protected function getDefaultParameters()
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$last = end($trace);
		$debugMode = static::detectDebugMode();

		return [
			'appDir' => isset($trace[2]['file']) ? dirname($trace[2]['file']) : NULL,
			'wwwDir' => isset($last['file']) ? dirname($last['file']) : NULL,
			'debugMode' => $debugMode,
			'productionMode' => !$debugMode,
			'consoleMode' => PHP_SAPI === 'cli',
		];
	}

	/**
	 * @param IPlugin $plugin
	 * @return void
	 */
	public function addPlugin(IPlugin $plugin)
	{
		$this->plugins[] = $plugin;
	}

	/**
	 * NETTE CONFIGURATOR ******************************************************
	 */

	/**
	 * @return Container
	 */
	public function createContainer()
	{
		$container = parent::createContainer();

		$this->trigger(IContainerPlugin::class, $this, $container);

		if ($this->isDebugMode()) {
			$this->trigger(IDebugContainerPlugin::class, $this, $container);
		}

		return $container;
	}

	/**
	 * HELPERS *****************************************************************
	 */

	/**
	 * @param Configurator $configurator
	 * @param Compiler $compiler
	 * @return void
	 */
	protected function compile(Configurator $configurator, Compiler $compiler)
	{
		$this->trigger(ICompilerPlugin::class, $configurator, $compiler);

		if ($this->isDebugMode()) {
			$this->trigger(IDebugCompilerPlugin::class, $configurator, $compiler);
		}
	}

	/**
	 * @param string $class
	 * @param array ...$params
	 * @return void
	 */
	protected function trigger($class, ...$params)
	{
		foreach ($this->plugins as $plugin) {
			// Skip different plugin
			if (!($plugin instanceof $class)) continue;

			call_user_func_array([$plugin, 'plugin'], $params);
		}
	}

}
