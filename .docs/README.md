# Bootstrap

## Content

- [ExtraConfigurator - bootstraping](#extraconfigurator)
- [PluggableConfigurator - plugin system](#pluggableconfigurator)

## ExtraConfigurator

The `ExtraConfigurator` extends `Configurator` and add a few extra methods for better usage in containers (Docker).

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
```

That's all.

-----

If you need to dig deeper, there are 2 main things for you:

- parse `NETTE__<>` environment variables (safety)
- detect debug mode from variable `NETTE_DEBUG`

```php
public function __construct()
{
    parent::__construct();

    $this->addParameters(self::getEnvironmentParameters());
    $this->setAutoDebugMode();
}
```

### Factories

If you don't want to create the `ExtraConfigurator` object that's fine. You can handle it via `setup` or `wrap` method.

The main difference between `setup` and `wrap` is that `wrap` function is called during `onCompile` event. It's alpha & omega, 
because it overrides all parameters you passed via `addParameters` or `addConfig`. Be aware of that!

```php
use Contributte\Bootstrap\ExtraConfigurator;
use Nette\Configurator;

$configurator = new Configurator();

ExtraConfigurator::wrap($configurator); // onCompile
// or
ExtraConfigurator::setup($configurator); // directly

$configurator->enableTracy(__DIR__ . '/../log');
$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

// ...
```

### Helpers

As you can see, there are a few static methods for you.

```php
$configurator->setDebugMode(ExtraConfigurator::parseEnvDebugMode());
$configurator->addParameters(ExtraConfigurator::parseEnvironmentParameters());
$configurator->addParameters(ExtraConfigurator::parseAllEnvironmentParameters());
```

### Debug mode

You can manage debug modes over `NETTE_DEBUG` variable.

- `NETTE_DEBUG`: true
- `NETTE_DEBUG`: 1
- `NETTE_DEBUG`: false
- `NETTE_DEBUG`: 0
- `NETTE_DEBUG`: 10.0.0.10
- `NETTE_DEBUG`: cookie@10.0.0.10

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
