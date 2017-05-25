<?php

namespace Contributte\Bootstrap;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\InvalidStateException;

class ExtraConfigurator extends Configurator
{

	/**
	 * Extra Configurator
	 */
	public function __construct()
	{
		parent::__construct();

		$this->addParameters(self::getEnvironmentParameters());
		$this->setEnvDebugMode();
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
	 * Collect environment parameters with NETTE__ prefix
	 *
	 * @return array
	 */
	public function getEnvironmentParameters()
	{
		return self::parseEnvironmentParameters();
	}

	/**
	 * Collect all environment variables
	 *
	 * @return array
	 */
	public function getAllEnvironmentParameters()
	{
		return self::parseAllEnvironmentParameters();
	}

	/**
	 * @return void
	 */
	public function setEnvDebugMode()
	{
		$this->setDebugMode(self::parseEnvDebugMode());
	}

	/**
	 * FACTORIES ***************************************************************
	 */

	/**
	 * Setup debug mode and add parameters immediately
	 *
	 * @param Configurator $configurator
	 * @return Configurator
	 */
	public static function setup(Configurator $configurator)
	{
		$configurator->setDebugMode(self::parseEnvDebugMode());
		$configurator->addParameters(self::parseEnvironmentParameters());

		return $configurator;
	}

	/**
	 * Setup debug mode and add parameters at compile time
	 *
	 * @param Configurator $configurator
	 * @return Configurator
	 */
	public static function wrap(Configurator $configurator)
	{
		$configurator->setDebugMode(self::parseEnvDebugMode());
		$configurator->onCompile[] = function (Configurator $configurator, Compiler $compiler) {
			$compiler->addConfig(['parameters' => self::parseEnvironmentParameters()]);
		};

		return $configurator;
	}

	/**
	 * STATIC ******************************************************************
	 */

	/**
	 * Parse environment parameters with NETTE__ prefix
	 *
	 * @return array
	 */
	public static function parseEnvironmentParameters()
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
			// Ensure value
			$value = getenv($key);
			if (strpos($key, 'NETTE__') === 0 && $value !== FALSE) {
				// Parse NETTE__{NAME-1}__{NAME-N}
				$keys = explode('__', strtolower(substr($key, 7)));
				// Make array structure
				$map($parameters, $keys, $value);
			}
		}

		return $parameters;
	}

	/**
	 * Parse all environment variables
	 *
	 * @return array
	 */
	public static function parseAllEnvironmentParameters()
	{
		$parameters = [];
		foreach ($_SERVER as $key => $value) {
			// Ensure value
			$value = getenv($key);
			if ($value !== FALSE) {
				$parameters[$key] = $value;
			}
		}

		return $parameters;
	}

	/**
	 * @return bool
	 */
	public static function parseEnvDebugMode()
	{
		$debug = getenv('NETTE_DEBUG');
		if ($debug !== FALSE) {
			$value = $debug;

			if ($value === 'true' || $value === '1') {
				$debug = TRUE;
			} else if ($value === 'false' || $value === '0') {
				$debug = FALSE;
			}

			return $debug;
		}

		return FALSE;
	}

}
