# Bootstrap

## Content

- [Configurator - bootstraping](#configurator)

## Configurator

This extended `Configurator` has a few extra methods for better usage in containers.

### Environment variables

You can now setup your config parameters over environment variables.

Environment variable must follow this pattern: `NETTE__{NAME}`, `NETTE__{NAME}__{NAME2}`.

```bash
export NETTE__DATABASE__USER=test
export NETTE__DATABASE__HOST=localhost
```

Just create our configurator object.

```php
use Contributte\Bootstrap\Configurator;

$configurator = new Configurator();
```

That's all.

### Debug mode

You can manage debug modes over `NETTE_DEBUG` varible.

- `NETTE_DEBUG`: true
- `NETTE_DEBUG`: false
- `NETTE_DEBUG`: 10.0.0.10
- `NETTE_DEBUG`: cookie@10.0.0.10
