<?php

namespace Contributte\Bootstrap;

use Nette\Configurator;
use Nette\InvalidStateException;

class ExtraConfigurator extends Configurator
{

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
	 * @param string $fileName
	 * @return void
	 */
	public function setFileDebugMode($fileName = NULL)
	{
		// Given file name or default file path
		$appDir = $this->parameters['appDir'] ? $this->parameters['appDir'] : NULL;
		if (!$fileName && !$appDir) return;

		// Try to load file
		$content = @file_get_contents($fileName ?: $appDir . '/../.debug');
		if ($content === FALSE) return;

		// File exists with no content
		if ($content === '') {
			$this->setDebugMode(TRUE);
			return;
		}

		$debug = self::parseDebugValue(trim($content));
		$this->setDebugMode($debug);
	}

	/**
	 * @return void
	 */
	public function addEnvParameters()
	{
		$this->addParameters(self::getEnvironmentParameters());
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
			return self::parseDebugValue($debug);
		}

		return FALSE;
	}

	/**
	 * HELPERS ******************************************************************
	 */

	/**
	 * @param mixed $debug
	 * @return mixed
	 */
	public static function parseDebugValue($debug)
	{
		$value = $debug;

		if (strtolower($value) === 'true' || $value === '1') {
			$debug = TRUE;
		} else if (strtolower($value) === 'false' || $value === '0') {
			$debug = FALSE;
		}

		return $debug;
	}

}
