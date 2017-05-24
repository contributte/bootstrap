<?php

use Ninjify\Nunjuck\Environment;

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

// Configure environment
Environment::setupTester();
Environment::setupTimezone();
Environment::setupVariables(__DIR__);

/**
 * @param string $key
 * @param mixed $value
 * @return void
 */
function env($key, $value)
{
	$_SERVER[$key] = $value;
	putenv(sprintf('%s=%s', $key, $value));
}
