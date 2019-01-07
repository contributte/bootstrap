<?php declare(strict_types = 1);

namespace Tests\Mocks;

use Contributte\Bootstrap\ExtraConfigurator;

final class MockExtraConfigurator extends ExtraConfigurator
{

	public function __construct()
	{
		parent::__construct();
		$this->addParameters(['__random' => microtime(true) + mt_rand(0, 1000000)]);
	}

}
