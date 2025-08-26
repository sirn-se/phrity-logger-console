<?php

namespace Phrity\Logger\Console;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

enum Verbosity: int
{
    case Silent = OutputInterface::VERBOSITY_SILENT;
    case Quiet = OutputInterface::VERBOSITY_QUIET;
    case Normal = OutputInterface::VERBOSITY_NORMAL;
    case Verbose = OutputInterface::VERBOSITY_VERBOSE;
    case VeryVerbose = OutputInterface::VERBOSITY_VERY_VERBOSE;
    case Debug = OutputInterface::VERBOSITY_DEBUG;

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
}
