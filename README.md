<p align="center"><img src="docs/logotype.png" alt="Phrity Logger Console" width="100%"></p>

[![Build Status](https://github.com/sirn-se/phrity-logger-console/actions/workflows/acceptance.yml/badge.svg)](https://github.com/sirn-se/phrity-logger-console/actions)

# Phrity Logger Console

A [PSR-3](https://www.php-fig.org/psr/psr-3/) compatible console logger.
Useful when running local tests and various console applications.

## Installation

Install with [Composer](https://getcomposer.org/);
```
composer require phrity/logger-console
```

## Verbosity

Verbosity level can be set on the logger.

```php
use Phrity\Logger\Console\{
    ConsoleLogger,
    Verbosity,
};
$logger = new ConsoleLogger(verbosity: Verbosity::Verbose);
```

Available levels (default: `Verbosity::Normal`);

* `Verbosity::Silent`
* `Verbosity::Quiet`
* `Verbosity::Normal`
* `Verbosity::Verbose`
* `Verbosity::VeryVerbose`
* `Verbosity::Debug`

## Output format

Output format can be specified using string with replacers.

```php
use Phrity\Logger\Console\ConsoleLogger;
$logger = new ConsoleLogger(format: '{datetime} {level} {message} - {context}');
```

Available replacers;

* `datetime` - ISO datetime string
* `level` - Log level string
* `message` - Log message (interpolated)
* `context` - String representation of context data

Default format is `'{datetime} [{level}] {message}'`.

## CLI options

By enabling CLI options, verbosity can be set as console argument.

```php
use Phrity\Logger\Console\ConsoleLogger;
$logger = new ConsoleLogger(cliOptions: true);
```

Available verbosity argument;

* `--silent`
* `--quiet` -q
* `--verbose=1` `-v`
* `--verbose=2` `-vv`
* `--verbose=3` `-vvv` `--debug`

## Versions

| Version | PHP | |
| --- | --- | --- |
| `1.0` | `^8.1` | Initial version |
