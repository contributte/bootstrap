![](https://heatbadger.now.sh/github/readme/contributte/bootstrap/)

<p align=center>
	<a href="https://github.com/contributte/bootstrap/actions"><img src="https://badgen.net/github/checks/contributte/bootstrap/master?cache=300"></a>
	<a href="https://codecov.io/gh/contributte/bootstrap"><img src="https://badgen.net/codecov/c/github/contributte/bootstrap?cache=300"></a>
	<a href="https://packagist.org/packages/contributte/bootstrap"><img src="https://badgen.net/packagist/dm/contributte/bootstrap"></a>
	<a href="https://packagist.org/packages/contributte/bootstrap"><img src="https://badgen.net/packagist/v/contributte/bootstrap"></a>
</p>
<p align=center>
	<a href="https://packagist.org/packages/contributte/bootstrap"><img src="https://badgen.net/packagist/php/contributte/bootstrap"></a>
	<a href="https://github.com/contributte/bootstrap"><img src="https://badgen.net/github/license/contributte/bootstrap"></a>
	<a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
	<a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
	<a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Bootstrap helpers for Nette applications, focused on environment-aware configuration and pluggable configurator extensions.

## Versions

| State  | Version | Branch   | Nette | PHP     |
|--------|---------|----------|-------|---------|
| dev    | `^0.8`  | `master` | 3.3+  | `>=8.2` |
| stable | `^0.7`  | `master` | 3.3+  | `>=8.2` |

## Installation

To install the latest version of `contributte/bootstrap` use [Composer](https://getcomposer.org).

```bash
composer require contributte/bootstrap
```

## ExtraConfigurator

The `ExtraConfigurator` extends `Configurator` and adds a few methods for better usage in containers (Docker).

- `setFileDebugMode($fileName = NULL)`
- `setEnvDebugMode()`
- `addEnvParameters()`

### Debug mode

We added two methods to help you detect the debug mode. You can either manage the debug mode via `NETTE_DEBUG` environmental variable and detect it this way:

```php
use Contributte\Bootstrap\ExtraConfigurator;

$configurator = new ExtraConfigurator();
$configurator->setEnvDebugMode();
```

or via a file. If no file is supplied as a parameter, it looks for `.debug` file in the root directory. The sole existence of the file with no content will set the debug mode to `TRUE`.

```php
use Contributte\Bootstrap\ExtraConfigurator;

$configurator = new ExtraConfigurator();
$configurator->setFileDebugMode(__DIR__ . '/../.debug');
```

Valid values for the ENV variable `NETTE_DEBUG` and the file are:

- true
- 1
- false
- 0
- 10.0.0.10
- cookie@10.0.0.10

### Environment variables

You can now setup your config parameters over environment variables.

Environment variable must follow this pattern: `NETTE__{NAME}`, `NETTE__{NAME}__{NAME2}`.

```bash
export NETTE__DATABASE__USER=test
export NETTE__DATABASE__HOST=localhost
```

Just create your configurator object.

```php
use Contributte\Bootstrap\ExtraConfigurator;

$configurator = new ExtraConfigurator();
$configurator->addEnvParameters();
```

That's all.

### Parse case

By default `NETTE__FOO` is `NETTE__` stripped and `FOO` is converted to lowercase. You can change it to natural parse case.

For example `NETTE_USer=felix` will produce:

```php
$configurator->setParseCase($configurator::PARSE_NATURAL); // USer
$configurator->setParseCase($configurator::PARSE_LOWERCASE); // user
$configurator->setParseCase($configurator::PARSE_UPPERCASE); // USER
```

### Parse delimiter

By default we have `__` delimiter. It can be changed to support `NETTE_FOO` or `NETTE.FOO`.

```php
$configurator->setParseDelimiter('_');
$configurator->setParseDelimiter('.');
```

### Helpers

You can also use these static methods for parsing ENV variables and setting the debug mode from the ENV variable.

```php
$configurator->setDebugMode(ExtraConfigurator::parseEnvDebugMode());
$configurator->addParameters(ExtraConfigurator::parseEnvironmentParameters());
$configurator->addParameters(ExtraConfigurator::parseAllEnvironmentParameters());
```

## PluggableConfigurator

There's a need to organize bulk of codes together, we call them plugins. Official `Nette\Configurator` does not support any type of plugin, so `PluggableConfigurator` was created.

```php
use Contributte\Bootstrap\PluggableConfigurator;

$pluggable = new PluggableConfigurator();

$pluggable->addPlugin(new MyBeforeContainerIsLoadedPlugin());
$pluggable->addPlugin(new SpecialOnlyInDebugModePlugin());
```

You can easily add a new plugin via `addPlugin($plugin)` method.

There are some types of plugin.

| Plugin                  | Triggers                   | Arguments               | Mode  |
|-------------------------|----------------------------|-------------------------|-------|
| `IConfigurationPlugin`  | before `createContainer`   | Configurator            | ALL   |
| `IContainerPlugin`      | after `createContainer`    | Configurator, Container | ALL   |
| `IDebugContainerPlugin` | after `createContainer`    | Configurator, Container | DEBUG |
| `ICompilerPlugin`       | during `generateContainer` | Configurator, Compiler  | ALL   |
| `IDebugCompilerPlugin`  | during `generateContainer` | Configurator, Compiler  | DEBUG |

## Development

See [how to contribute](https://contributte.org/contributing.html) to this package.

This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
