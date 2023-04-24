<?php declare(strict_types = 1);

namespace Contributte\Bootstrap;

use Nette\Bootstrap\Configurator;
use Nette\InvalidStateException;

class ExtraConfigurator extends Configurator
{

	public const PARSE_NATURAL = 1;
	public const PARSE_LOWERCASE = 2;
	public const PARSE_UPPERCASE = 3;

	/** @var int How to parse the parameters */
	protected static $parseCase = self::PARSE_LOWERCASE;

	/** @var non-empty-string Sections separator */
	protected static $parseDelimiter = '__';

	/**
	 * Collect default parameters
	 *
	 * @return mixed[]
	 */
	protected function getDefaultParameters(): array
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		$last = (array) end($trace);
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
	 * Collect environment parameters with NETTE{delimiter=__} prefix
	 *
	 * @return mixed[]
	 */
	public function getEnvironmentParameters(): array
	{
		return static::parseEnvironmentParameters();
	}

	/**
	 * Collect all environment variables
	 *
	 * @return mixed[]
	 */
	public function getAllEnvironmentParameters(): array
	{
		return static::parseAllEnvironmentParameters();
	}

	public function setEnvDebugMode(): void
	{
		$this->setDebugMode(static::parseEnvDebugMode());
	}

	public function setFileDebugMode(?string $fileName = null): void
	{
		// Given file name or default file path
		$appDir = $this->staticParameters['appDir'] ?? null;
		if ($fileName === null && $appDir === null) return;

		// Try to load file
		$content = @file_get_contents($fileName ?? $appDir . '/../.debug');
		if ($content === false) return;

		// File exists with no content
		if ($content === '') {
			$this->setDebugMode(true);

			return;
		}

		$debug = static::parseDebugValue(trim($content));
		$this->setDebugMode($debug);
	}

	public function addEnvParameters(): void
	{
		$this->addStaticParameters($this->getEnvironmentParameters());
	}

	/**
	 * Parse environment parameters with NETTE{delimiter=__} prefix
	 *
	 * @return mixed[]
	 */
	public static function parseEnvironmentParameters(): array
	{
		return static::parseParameters($_SERVER, 'NETTE' . self::$parseDelimiter);
	}

	/**
	 * Parse given parameters with custom prefix
	 *
	 * @param mixed[] $variables
	 * @return mixed[]
	 */
	public static function parseParameters(array $variables, string $prefix): array
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
		foreach ($variables as $key => $value) {
			// Ensure value
			$value = getenv($key);
			if (strpos($key, $prefix) === 0 && $value !== false) {
				// Parse PREFIX{delimiter=__}{NAME-1}{delimiter=__}{NAME-N}
				$keys = static::parseParameter(substr($key, strlen($prefix)));
				// Make array structure
				$map($parameters, $keys, $value);
			}
		}

		return $parameters;
	}

	/**
	 * @return mixed[]
	 */
	public static function parseParameter(string $key): array
	{
		if (self::$parseCase === self::PARSE_LOWERCASE) {
			return explode(self::$parseDelimiter, strtolower($key));
		}

		if (self::$parseCase === self::PARSE_UPPERCASE) {
			return explode(self::$parseDelimiter, strtoupper($key));
		}

		return explode(self::$parseDelimiter, $key);
	}

	public function setParseCase(int $mode): void
	{
		self::$parseCase = $mode;
	}

	/** @param non-empty-string $delimiter */
	public function setParseDelimiter(string $delimiter): void
	{
		self::$parseDelimiter = $delimiter;
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
			return static::parseDebugValue($debug);
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
