<?php declare(strict_types = 1);

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
 * @param string|float|bool $value
 */
function env(string $key, $value): void
{
	$_SERVER[$key] = $value;
	putenv(sprintf('%s=%s', $key, $value));
}
