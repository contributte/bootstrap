<?php declare(strict_types = 1);

use Contributte\Tester\Environment;

if (@!include __DIR__ . '/../vendor/autoload.php') {
	echo 'Install Nette Tester using `composer update --dev`';
	exit(1);
}

// Configure environment
Environment::setupTester();
Environment::setupTimezone();
Environment::setupVariables(__DIR__);

function env(string $key, string|float|bool $value): void
{
	$_SERVER[$key] = $value;
	putenv(sprintf('%s=%s', $key, $value));
}
