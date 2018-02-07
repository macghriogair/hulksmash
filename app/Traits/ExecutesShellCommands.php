<?php

/**
 * @date    2018-02-01
 * @file    ExecutesShellCommands.php
 * @author  Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

declare(strict_types = 1);

namespace App\Traits;

trait ExecutesShellCommands
{
    /**
     * Executes a shell command.
     *
     * @param  string|array
     * @return self
     */
    protected function runShellCommand($cmd) : self
    {
        $args = func_get_args();
        $cmd = self::processCommand($args);

        exec($cmd . '2>&1', $output, $exitCode);

        if (0 !== $exitCode) {
            throw new \RuntimeException("Command '$cmd' failed with exit code $exitCode.");
        }

        return $this;
    }

    protected static function processCommand(array $args) : string
    {
        $cmd = [];
        $executable = array_shift($args);
        foreach ($args as $arg) {
            if (is_array($arg)) {
                foreach ($arg as $key => $value) {
                    $c = '';
                    if (is_string($key)) {
                        $c = "$key ";
                    }
                    $cmd[] = $c . escapeshellarg($value);
                }
            } elseif (is_scalar($arg) && ! is_bool($arg)) {
                $cmd[] = escapeshellarg($arg);
            }
        }

        return "$executable " . implode(' ', $cmd);
    }
}
