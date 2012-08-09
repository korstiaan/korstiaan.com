<?php

namespace Kors\Com\Composer;

use Symfony\Component\Process\Process,
    Symfony\Component\Process\PhpExecutableFinder;

use Composer\Script\Event;

/**
 * Composer callback scripts
 */
abstract class Console
{
    /**
     * Creates the necessary bootstrap asset symlinks
     *
     * @param Event $event
     */
    public static function createSymlinks(Event $event)
    {
        self::execCommand('symlink:create');
    }

    /**
     * Dumps assets defined with assetic
     *
     * @param Event $event
     */
    public static function asseticDump(Event $event)
    {
        self::execCommand('assetic:dump');
    }

    /**
     * Executes given console command
     *
     * @param string $cmd
     *
     * @throws \RuntimeException in case php executable can't be found
     * @throws \Exception        in case command wasn't successfully executed
     */
    protected static function execCommand($cmd)
    {
        $phpFinder = new PhpExecutableFinder();

        $php       = $phpFinder->find();

        if (empty($php)) {
            throw new \RuntimeException('Unable to locate PHP executable, please add it to your path');
        }

        $cmd     = escapeshellarg($php).' '.escapeshellarg(realpath('app/console.php')).' '.escapeshellarg($cmd);

        $process = new Process($cmd);

        $process->run(function($err, $buffer) {
            print $buffer;
        });

        if (!$process->isSuccessful()) {
            throw new \Exception("Running console cmd {$cmd} resulted in an error: {$process->getErrorOutput()}");
        }
    }
}
