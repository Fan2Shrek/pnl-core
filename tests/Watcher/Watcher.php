<?php

namespace Pnl\Test\Watcher;

use Closure;

class Watcher
{
    private array $filesMap = [];

    /** @var resource */
    private mixed $pointer;

    private Closure $closure;

    private ?Closure $exceptionHandler = null;

    private function observDir(string $dirName, string $parent = null): void
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

                $this->execute();
            }
        }
    }

    public function watch(string $path, Closure $closure): void
    {
        $this->closure = $closure;

        $this->execute();

        do {
            sleep(1);
            $this->observDir($path);
        } while (1);
    }

    private function execute(): void
    {
        echo "\033[2J\033[;H";

        try {
            ($this->closure)();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    private function handleException(\Throwable $e): void
    {
        if (null !== $this->exceptionHandler) {
            ($this->exceptionHandler)($e);
        } else {
            echo $e->getMessage();
        }
    }

    public function setExceptionHandler(Closure $exceptionHandler): void
    {
        $this->exceptionHandler = $exceptionHandler;
    }
}
