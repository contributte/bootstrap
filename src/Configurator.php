<?php

namespace Contributte\Bootstrap;

use Nette\Configurator as NConfigurator;
use Nette\InvalidStateException;

class Configurator extends NConfigurator
{

	/**
	 * @return array
	 */
	protected function getDefaultParameters()
	{
		$parameters = parent::getDefaultParameters();
		$parameters = array_merge($parameters, $this->getEnvironmentParameters());

		return $parameters;
	}

	/**
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
			$this->setDebugMode($_SERVER['NETTE_DEBUG']);
		}
	}

}
