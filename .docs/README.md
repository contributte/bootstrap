# Bootstrap

## Content

- [ExtraConfigurator - bootstraping](#extraconfigurator)
- [PluggableConfigurator - plugin system](#pluggableconfigurator)

## ExtraConfigurator

The `ExtraConfigurator` extends `Configurator` and adds a few methods for better usage in containers (Docker).

- `setFileDebugMode($fileName = NULL)`
- `setEnvDebugMode()`
- `addEnvParameters()`

### Debug mode

We added two methods to help you detect debug mode. You can either manage debug mode via `NETTE_DEBUG` environmental variable and detect it this way:

```php
$configurator = new ExtraConfigurator();
$configurator->setEnvDebugMode();
```

or via file. If no file supplied as parameter it looks for `.debug` in root directory. The sole existence of the file with no content will set debug mode as TRUE.

```php
$configurator = new ExtraConfigurator();
$configurator->setFileDebugMode(__DIR__ . '/../.debug');
```

Valid values for ENV variable `NETTE_DEBUG` and the file are:

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

Just create our configurator object.

```php
use Contributte\Bootstrap\ExtraConfigurator;

$configurator = new ExtraConfigurator();
$configurator->addEnvParameters();
```

That's all.

-----

### Helpers

You can also use these static methods for parsing ENV variables and setting debug mode from ENV variable.

```php
$configurator->setDebugMode(ExtraConfigurator::parseEnvDebugMode());
$configurator->addParameters(ExtraConfigurator::parseEnvironmentParameters());
$configurator->addParameters(ExtraConfigurator::parseAllEnvironmentParameters());
```

## PluggableConfigurator

There's a need to organize bulk of codes together, we call them plugins. Official `Nette\Configurator` does not support
any type of plugin, so `PluggableConfigurator` was created.

```php
use Contributte\Bootstrap\PluggableConfigurator;

$pluggable = new PluggableConfigurator();

$pluggable->addPlugin(new MyBeforeContainerIsLoadedPlugin());
$pluggable->addPlugin(new SpecialOnlyInDebugModePlugin());
```

You can easilly add a new plugin via `addPlugin($plugin)` method.

There are some types of plugin.

| Plugin                  | Triggers                   | Arguments               | Mode  |
|-------------------------|----------------------------|-------------------------|-------|
| `IConfigurationPlugin`  | before `createContainer`   | Configurator            | ALL   |
| `IContainerPlugin`      | after `createContainer`    | Configurator, Container | ALL   |
| `IDebugContainerPlugin` | after `createContainer`    | Configurator, Container | DEBUG |
| `ICompilerPlugin`       | during `generateContainer` | Configurator, Compiler  | ALL   |
| `IDebugCompilerPlugin`  | during `generateContainer` | Configurator, Compiler  | DEBUG |
