<?php declare(strict_types = 1);

namespace Contributte\Bootstrap;

use Contributte\Bootstrap\Plugin\ICompilerPlugin;
use Contributte\Bootstrap\Plugin\IConfigurationPlugin;
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
		$this->onCompile[] = function (Configurator $configurator, Compiler $compiler): void {
			$this->compile($configurator, $compiler);
		};
	}

	/**
	 * Collect default parameters
	 *
	 * @return mixed[]
	 */
	protected function getDefaultParameters(): array
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$last = end($trace);
		$debugMode = static::detectDebugMode();

		return [
			'appDir' => isset($trace[2]['file']) ? dirname($trace[2]['file']) : null,
			'wwwDir' => isset($last['file']) ? dirname($last['file']) : null,
			'debugMode' => $debugMode,
			'productionMode' => !$debugMode,
			'consoleMode' => PHP_SAPI === 'cli',
		];
	}

	public function addPlugin(IPlugin $plugin): void
	{
		$this->plugins[] = $plugin;
	}

	public function createContainer(): Container
	{
		$this->trigger(IConfigurationPlugin::class, $this);

		$container = parent::createContainer();

		$this->trigger(IContainerPlugin::class, $this, $container);

		if ($this->isDebugMode()) {
			$this->trigger(IDebugContainerPlugin::class, $this, $container);
		}

		return $container;
	}

	protected function compile(Configurator $configurator, Compiler $compiler): void
	{
		$this->trigger(ICompilerPlugin::class, $configurator, $compiler);

		if ($this->isDebugMode()) {
			$this->trigger(IDebugCompilerPlugin::class, $configurator, $compiler);
		}
	}

	/**
	 * @param mixed[] $params
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	protected function trigger(string $class, ...$params): void
	{
		foreach ($this->plugins as $plugin) {
			// Skip different plugin
			if (!($plugin instanceof $class)) continue;
			call_user_func_array([$plugin, 'plugin'], $params);
		}
	}

}
