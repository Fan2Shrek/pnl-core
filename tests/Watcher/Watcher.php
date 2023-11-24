<?php

namespace Pnl\Test\Watcher;

use Closure;

class Watcher
{
    private array $filesMap = [];

    /** @var resource */
    private mixed $pointer;

    private Closure $closure;

    public function observDir(string $dirName, string $parent = null): void
    {
        if (null !== $parent) {
            $dirName = $parent . '/' . $dirName;
        }

        foreach (array_diff(scandir($dirName), ['.', '..']) as $file) {
            $path =  $dirName . '/' . $file;

            if (is_dir($path)) {
                $this->observDir($file, $dirName);

                continue;
            }

            if (!isset($this->filesMap[$path])) {
                $this->filesMap[$path] = filemtime($path);

                continue;
            }

            if ($this->filesMap[$path] !== filemtime($path)) {
                $this->filesMap[$path] = filemtime($path);

                echo "\033[2J\033[;H";
                ($this->closure)();
            }
        }
    }

    public function watch(string $path, Closure $closure): void
    {
        $this->closure = $closure;

        ($this->closure)();

        while (1) {
            sleep(1);

            $this->observDir($path);
        }
    }
}
