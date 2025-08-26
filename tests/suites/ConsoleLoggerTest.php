<?php

declare(strict_types=1);

namespace Phrity\Logger\Console\Test;

use DateTime;
use PHPUnit\Framework\TestCase;
use Phrity\Logger\Console\{
    ConsoleLogger,
    Verbosity,
};
use Psr\Log\{
    InvalidArgumentException,
    LoggerInterface,
    LogLevel,
};

/**
 * ConsoleLogger test class.
 */
class ConsoleLoggerTest extends TestCase
{
    public function testClass(): void
    {
        $logger = new ConsoleLogger();
        $this->assertInstanceof(LoggerInterface::class, $logger);
    }

    public function testLogNormalVerbosity(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(format: '{level}|{message}');
        $logger->critical('a critical message');
        $logger->emergency('an emergency message');
        $logger->alert('an alert message');
        $logger->error('an error message');
        $logger->warning('a warning message');
        $logger->notice('a notice message');
        $logger->info('an info message');
        $logger->debug('a debug message');

        $result = StreamBuffer::stop();
        $expect = "critical|a critical message\n"
                . "emergency|an emergency message\n"
                . "alert|an alert message\n"
                . "error|an error message\n"
                . "warning|a warning message\n"
                ;
        $this->assertEquals($expect, $result);
    }

    public function testLogVerboseVerbosity(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(verbosity: Verbosity::Verbose, format: '{level}|{message}');
        $logger->critical('a critical message');
        $logger->emergency('an emergency message');
        $logger->alert('an alert message');
        $logger->error('an error message');
        $logger->warning('a warning message');
        $logger->notice('a notice message');
        $logger->info('an info message');
        $logger->debug('a debug message');

        $result = StreamBuffer::stop();
        $expect = "critical|a critical message\n"
                . "emergency|an emergency message\n"
                . "alert|an alert message\n"
                . "error|an error message\n"
                . "warning|a warning message\n"
                . "notice|a notice message\n"
                ;
        $this->assertEquals($expect, $result);
    }

    public function testLogVeryVerboseVerbosity(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(verbosity: Verbosity::VeryVerbose, format: '{level}|{message}');
        $logger->critical('a critical message');
        $logger->emergency('an emergency message');
        $logger->alert('an alert message');
        $logger->error('an error message');
        $logger->warning('a warning message');
        $logger->notice('a notice message');
        $logger->info('an info message');
        $logger->debug('a debug message');

        $result = StreamBuffer::stop();
        $expect = "critical|a critical message\n"
                . "emergency|an emergency message\n"
                . "alert|an alert message\n"
                . "error|an error message\n"
                . "warning|a warning message\n"
                . "notice|a notice message\n"
                . "info|an info message\n"
                ;
        $this->assertEquals($expect, $result);
    }

    public function testLogDebugVerbosity(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(verbosity: Verbosity::Debug, format: '{level}|{message}');
        $logger->critical('a critical message');
        $logger->emergency('an emergency message');
        $logger->alert('an alert message');
        $logger->error('an error message');
        $logger->warning('a warning message');
        $logger->notice('a notice message');
        $logger->info('an info message');
        $logger->debug('a debug message');

        $result = StreamBuffer::stop();
        $expect = "critical|a critical message\n"
                . "emergency|an emergency message\n"
                . "alert|an alert message\n"
                . "error|an error message\n"
                . "warning|a warning message\n"
                . "notice|a notice message\n"
                . "info|an info message\n"
                . "debug|a debug message\n"
                ;
        $this->assertEquals($expect, $result);
    }

    public function testLogSilentVerbosity(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(verbosity: Verbosity::Silent, format: '{level}|{message}');
        $logger->critical('a critical message');
        $logger->emergency('an emergency message');
        $logger->alert('an alert message');
        $logger->error('an error message');
        $logger->warning('a warning message');
        $logger->notice('a notice message');
        $logger->info('an info message');
        $logger->debug('a debug message');

        $result = StreamBuffer::stop();
        $this->assertEquals("", $result);
    }

    public function testLogQuietVerbosity(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(verbosity: Verbosity::Quiet, format: '{level}|{message}');
        $logger->critical('a critical message');
        $logger->emergency('an emergency message');
        $logger->alert('an alert message');
        $logger->error('an error message');
        $logger->warning('a warning message');
        $logger->notice('a notice message');
        $logger->info('an info message');
        $logger->debug('a debug message');

        $result = StreamBuffer::stop();
        $this->assertEquals("", $result);
    }

    public function testStringableLogLevel(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(format: '{level}|{message}');
        $logger->log(new StringableObject(LogLevel::CRITICAL), 'a critical message');

        $result = StreamBuffer::stop();
        $this->assertEquals("critical|a critical message\n", $result);
    }

    public function testInvalidLogLevelString(): void
    {
        $logger = new ConsoleLogger();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid LogLevel: invalid.');
        $logger->log('invalid', 'invalid LogLevel using string');
    }

    public function testInvalidLogLevelType(): void
    {
        $logger = new ConsoleLogger();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid LogLevel: array.');
        $logger->log([1, 2, 3], 'invalid LogLevel using array');
    }

    public function testContextInterpolation(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(format: '{message}');
        $logger->error('a:{a}|b:{b}|c:{c}|d:{d}|e:{e}|f:{f}|g:{g}|h:{h}', [
            'a' => 'a string',
            'b' => 1234,
            'c' => true,
            'd' => null,
            'e' => new StringableObject('stringable'),
            'f' => new InvalidArgumentException('my exception'),
            'g' => new DateTime('2025-08-13'),
            'h' => Verbosity::Silent,
        ]);
        $result = StreamBuffer::stop();
        $expect = "a:a string|b:1234|c:true|d:null|e:stringable|f:my exception|g:2025-08-13T00:00:00+00:00|h:Silent\n";
        $this->assertEquals($expect, $result);
    }

    public function testContextAccessInterpolation(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(format: '{message}');
        $logger->error('m:{e.message}|c:{e.code}|e:{e}', [
            'e' => new InvalidArgumentException('my exception', 1234),
        ]);
        $result = StreamBuffer::stop();
        $expect = "m:my exception|c:1234|e:my exception\n";
        $this->assertEquals($expect, $result);
    }

    public function testContextOutput(): void
    {
        StreamBuffer::start();
        $logger = new ConsoleLogger(format: '{message} {context}');
        $logger->error('Context', [
            'a' => 'a string',
            'b' => 1234,
            'c' => true,
            'd' => null,
            'e' => new StringableObject('stringable'),
            'f' => new InvalidArgumentException('my exception'),
            'g' => new DateTime('2025-08-13'),
            'h' => Verbosity::Silent,
        ]);
        $result = StreamBuffer::stop();
        $expect = "Context {a: \"a string\", b: 1234, c: true, d: null, e: stringable, "
                . "f: my exception, g: 2025-08-13T00:00:00+00:00, h: Silent}\n";
        $this->assertEquals($expect, $result);
    }

    public function testCliVerbosity(): void
    {
        $argvOriginal = $_SERVER['argv'];

        StreamBuffer::start();
        $_SERVER['argv'] = array_merge($argvOriginal, ['--verbose=3']);
        $logger = new ConsoleLogger(cliOptions: true, format: '{level}|{message}');
        $logger->debug('a debug message');
        $result = StreamBuffer::stop();
        $this->assertEquals("debug|a debug message\n", $result);
        $_SERVER['argv'] = $argvOriginal;

        StreamBuffer::start();
        $_SERVER['argv'] = array_merge($argvOriginal, ['--verbose', '1']);
        $logger = new ConsoleLogger(cliOptions: true, format: '{level}|{message}');
        $logger->debug('a debug message');
        $result = StreamBuffer::stop();
        $this->assertEquals("", $result);
        $_SERVER['argv'] = $argvOriginal;

        StreamBuffer::start();
        $_SERVER['argv'] = array_merge($argvOriginal, ['--verbose', '2']);
        $logger = new ConsoleLogger(cliOptions: true, format: '{level}|{message}');
        $logger->debug('a debug message');
        $result = StreamBuffer::stop();
        $this->assertEquals("", $result);
        $_SERVER['argv'] = $argvOriginal;

        StreamBuffer::start();
        $_SERVER['argv'] = array_merge($argvOriginal, ['--verbose', '3']);
        $logger = new ConsoleLogger(cliOptions: true, format: '{level}|{message}');
        $logger->debug('a debug message');
        $result = StreamBuffer::stop();
        $this->assertEquals("debug|a debug message\n", $result);
        $_SERVER['argv'] = $argvOriginal;
    }

    public function testNotCli(): void
    {
        // Override php_sapi_name function, return false
        $GLOBALS['override_php_sapi_name'] = 'not_cli';
        $GLOBALS['override_defined'] = false;

        StreamBuffer::start();
        $logger = new ConsoleLogger();
        $logger->error("Should not output");
        $result = StreamBuffer::stop();
        $this->assertEquals("", $result);

        unset($GLOBALS['override_php_sapi_name']);
        unset($GLOBALS['override_defined']);
    }
}
