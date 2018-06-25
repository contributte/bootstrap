<?php declare(strict_types = 1);

namespace Contributte\Bootstrap;

use Nette\Configurator;
use Nette\InvalidStateException;

class ExtraConfigurator extends Configurator
{

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
			'appDir' => isset($trace[1]['file']) ? dirname($trace[1]['file']) : null,
			'wwwDir' => isset($last['file']) ? dirname($last['file']) : null,
			'debugMode' => $debugMode,
			'productionMode' => !$debugMode,
			'consoleMode' => PHP_SAPI === 'cli',
		];
	}

	/**
	 * Collect environment parameters with NETTE__ prefix
	 *
	 * @return mixed[]
	 */
	public function getEnvironmentParameters(): array
	{
		return self::parseEnvironmentParameters();
	}

	/**
	 * Collect all environment variables
	 *
	 * @return mixed[]
	 */
	public function getAllEnvironmentParameters(): array
	{
		return self::parseAllEnvironmentParameters();
	}

	public function setEnvDebugMode(): void
	{
		$this->setDebugMode(self::parseEnvDebugMode());
	}

	public function setFileDebugMode(?string $fileName = null): void
	{
		// Given file name or default file path
		$appDir = $this->parameters['appDir'] ?? null;
		if ($fileName === null && $appDir === null) return;

		// Try to load file
		$content = @file_get_contents($fileName ?: $appDir . '/../.debug');
		if ($content === false) return;

		// File exists with no content
		if ($content === '') {
			$this->setDebugMode(true);
			return;
		}

		$debug = self::parseDebugValue(trim($content));
		$this->setDebugMode($debug);
	}

	public function addEnvParameters(): void
	{
		$this->addParameters($this->getEnvironmentParameters());
	}

	/**
	 * Parse environment parameters with NETTE__ prefix
	 *
	 * @return mixed[]
	 */
	public static function parseEnvironmentParameters(): array
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
			if (strpos($key, 'NETTE__') === 0 && $value !== false) {
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
	 * @return mixed[]
	 */
	public static function parseAllEnvironmentParameters(): array
	{
		$parameters = [];
		foreach ($_SERVER as $key => $value) {
			// Ensure value
			$value = getenv($key);
			if ($value !== false) {
				$parameters[$key] = $value;
			}
		}

		return $parameters;
	}

	/**
	 * @return bool|string
	 */
	public static function parseEnvDebugMode()
	{
		$debug = getenv('NETTE_DEBUG');
		if ($debug !== false) {
			return self::parseDebugValue($debug);
		}

		return false;
	}

	/**
	 * @return bool|string
	 */
	public static function parseDebugValue(string $debug)
	{
		$value = $debug;

		if (strtolower($value) === 'true' || $value === '1') {
			$debug = true;
		} elseif (strtolower($value) === 'false' || $value === '0') {
			$debug = false;
		}

		return $debug;
	}

}
