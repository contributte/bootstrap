<?php

namespace Contributte\Bootstrap;

use Nette\Configurator as NConfigurator;
use Nette\InvalidStateException;

class Configurator extends NConfigurator
{

	/**
	 * Configurator
	 */
	public function __construct()
	{
		parent::__construct();

		$this->addParameters($this->getEnvironmentParameters());
	}

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
	 * Collect environment parameters
	 *
	 * @return array
	 */
	protected function getEnvironmentParameters()
	{
		$map = function (&$array, array $keys, $value) use (&$map) {
			if (count($keys) <= 0) return $value;

			$key = array_shift($keys);

			if (!is_array($array)) {
				throw new InvalidStateException(sprintf('Invalid structure for key "%s" value "%s"', implode($keys), $value));
			}

			if (!array_key_exists($key, $array)) {
				$array[$key] = [];
			}

			// Recursive
			$array[$key] = $map($array[$key], $keys, $value);

			return $array;
		};

		$parameters = [];
		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'NETTE__') === 0) {
				// Parse NETTE__{NAME-1}__{NAME-N}
				$keys = explode('__', strtolower(substr($key, 7)));
				// Make array structure
				$map($parameters, $keys, $value);
			}
		}

		return $parameters;
	}

	/**
	 * @return void
	 */
	public function autoDebugMode()
	{
		if (isset($_SERVER['NETTE_DEBUG'])) {
			$debug = $_SERVER['NETTE_DEBUG'];
			$value = strtolower($debug);

			if ($value === 'true' || $value === '1') {
				$debug = TRUE;
			} else if ($value === 'false' || $value === '0') {
				$debug = FALSE;
			}

			$this->setDebugMode($debug);
		}
	}

}
