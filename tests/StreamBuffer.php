<?php

declare(strict_types=1);

namespace Phrity\Logger\Console\Test;

use php_user_filter;

class StreamBuffer extends php_user_filter
{
    /** @var resource|false $stdout */
    private static $stdout = false;
    /** @var resource|false $stderr */
    private static $stderr = false;
    private static bool $registered = false;
    private static string $buffer = '';

    public function filter($in, $out, &$consumed, $closing): int
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            self::$buffer .= $bucket->data;
            $bucket->data = '';
            $consumed += (int)$bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }

    public static function start(): void
    {
        if (!self::$registered) {
            self::$registered = stream_filter_register('stream-buffer', self::class);
        }
        self::$stdout = stream_filter_append(STDOUT, 'stream-buffer', STREAM_FILTER_WRITE);
        self::$stderr = stream_filter_append(STDERR, 'stream-buffer', STREAM_FILTER_WRITE);
    }

    public static function stop(): string
    {
        if (self::$stdout) {
            stream_filter_remove(self::$stdout);
        }
        if (self::$stderr) {
            stream_filter_remove(self::$stderr);
        }
        self::$stdout = self::$stderr = false;
        $buffer = self::$buffer;
        self::$buffer = '';
        return $buffer;
    }
}
