<?php

namespace Phrity\Logger\Console;

use DateTime;
use Phrity\Util\Interpolator\Interpolator;
use Phrity\Util\Transformer\{
    BasicTypeConverter,
    DateTimeConverter,
    EnumConverter,
    FirstMatchResolver,
    ReadableConverter,
    StringableConverter,
    StringResolver,
    ThrowableConverter,
    Type,
};
use Psr\Log\{
    InvalidArgumentException,
    LoggerTrait,
    LoggerInterface,
    LogLevel,
};
use Stringable;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\{
    ConsoleOutput,
    OutputInterface,
};

/**
 */
class ConsoleLogger implements LoggerInterface
{
    use LoggerTrait;

    /** @var array<string> $logLevels */
    private static array $logLevels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];

    private ConsoleOutput $consoleOutput;
    private Interpolator $interpolator;
    private StringResolver $resolver;
    private string $format;

    public function __construct(
        Verbosity $verbosity = Verbosity::Normal,
        string $format = '{datetime} [{level}] {message}',
        bool $cliOptions = false,
    ) {
        $this->consoleOutput = new ConsoleOutput();
        $this->interpolator = new Interpolator(transformer: new FirstMatchResolver([
            new ThrowableConverter(),
            new DateTimeConverter(),
            new EnumConverter(),
            new ReadableConverter(),
            new StringableConverter(),
            new BasicTypeConverter(),
        ]));
        $this->resolver = new StringResolver(transformer: new FirstMatchResolver([
            new ThrowableConverter(),
            new DateTimeConverter(),
            new EnumConverter(),
            new ReadableConverter(),
            new StringableConverter(),
        ]));
        $this->format = $format;
        $this->setConsoleVerbosity($verbosity, $cliOptions);
    }

    public function log(mixed $level, string|Stringable $message, array $context = []): void
    {
        if (!$this->isCli()) {
            return;
        }
        $level = $this->getLogLevel($level);
        $message = $this->interpolator->interpolate($message, $context);
        $message = $this->interpolator->interpolate($this->format, [
            'datetime' => new DateTime(),
            'level' => $level,
            'message' => $message,
            'context' => $this->resolver->transform($context, Type::STRING),
        ]);
        $verbosity = Verbosity::byLogLevel($level);
        $this->consoleOutput->writeln($message, $verbosity->value());
    }

    private function isCli(): bool
    {
        return php_sapi_name() == 'cli' || defined('STDIN');
    }

    private function setConsoleVerbosity(Verbosity $verbosity, bool $cliOptions): void
    {
        $argvInput = new ArgvInput();
        $verbosity = $cliOptions ? match (true) {
            $argvInput->hasParameterOption(['--quiet', '-q']) => Verbosity::Quiet,
            $argvInput->hasParameterOption(['--debug', '--verbose=3', '-vvv']) => Verbosity::Debug,
            $argvInput->hasParameterOption(['--verbose=2', '-vv']) => Verbosity::VeryVerbose,
            $argvInput->hasParameterOption(['--verbose=1', '-v']) => Verbosity::Verbose,
            $argvInput->hasParameterOption(['--verbose']) => match ((int)$argvInput->getParameterOption('--verbose')) {
                1 => Verbosity::Verbose,
                2 => Verbosity::VeryVerbose,
                3 => Verbosity::Debug,
                default => $verbosity,
            },
            default => $verbosity,
        } : $verbosity;
        $this->consoleOutput->setVerbosity($verbosity->value());
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getLogLevel(mixed $level): string
    {
        if ($level instanceof Stringable) {
            $level = (string)$level;
        }
        if (in_array($level, self::$logLevels)) {
            return $level;
        }
        throw new InvalidArgumentException(
            $this->interpolator->interpolate('invalid LogLevel: {level}.', ['level' => $level])
        );
    }
}
