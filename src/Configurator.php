<?php

namespace Contributte\Bootstrap;

use Nette\Configurator as NConfigurator;

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
		$parameters = [];
		foreach ($_SERVER as $key => $value) {
			if (strpos($key, 'NETTE__') === 0) {
				$parameters[strtolower(str_replace('__', '.', substr($key, 7)))] = $value;
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
