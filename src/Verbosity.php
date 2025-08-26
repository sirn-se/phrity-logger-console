<?php

namespace Phrity\Logger\Console;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

enum Verbosity
{
    case Quiet;
    case Normal;
    case Verbose;
    case VeryVerbose;
    case Debug;

    public static function byLogLevel(mixed $level): self
    {
        return match ($level) {
            LogLevel::CRITICAL,
            LogLevel::EMERGENCY,
            LogLevel::ALERT,
            LogLevel::ERROR,
            LogLevel::WARNING => Verbosity::Normal,
            LogLevel::NOTICE => Verbosity::Verbose,
            LogLevel::INFO => Verbosity::VeryVerbose,
            LogLevel::DEBUG => Verbosity::Debug,
            default => Verbosity::Quiet,
        };
    }

    /** @return 16|32|64|128|256 */
    public function value(): int
    {
        return match ($this) {
            self::Quiet => OutputInterface::VERBOSITY_QUIET,
            self::Normal => OutputInterface::VERBOSITY_NORMAL,
            self::Verbose => OutputInterface::VERBOSITY_VERBOSE,
            self::VeryVerbose => OutputInterface::VERBOSITY_VERY_VERBOSE,
            self::Debug => OutputInterface::VERBOSITY_DEBUG,
        };
    }
}
