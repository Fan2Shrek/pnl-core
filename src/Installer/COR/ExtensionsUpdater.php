<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\PnlConfig;

class ExtensionsUpdater extends AbsractInstaller
{
    private const EXTENSION_FILE = __DIR__ . '/../../../config/extensions.php';

    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        $this->writeWithStyle("Updating ", 'green');
        $this->writeWithStyle("config/extensions.php...", 'basic');

        $this->writeln('');

        if (!file_exists(self::EXTENSION_FILE)) {
            $this->createExtensionFile();
        }

        $this->updateExtensionFile($pnlConfig->mainClass);

        $this->writeWithStyle("✅ Done", 'green');
        $this->writeln('');
        $this->writeln('');

        return $pnlConfig;
    }

    private function updateExtensionFile(string $add): void
    {
        $extensions = require self::EXTENSION_FILE;

        if (in_array($add, $extensions)) {
            $this->writeWithStyle(sprintf("🟨 %s already exists", $add), 'green');
            $this->writeln('');

            return;
        }

        $extensions[] = $add;

        $content = "<?php\n\nreturn [\n";

        foreach ($extensions as $extension) {
            $content .= "\t$extension::class,\n";
        }

        $content .= '];';

        $file = fopen(self::EXTENSION_FILE, 'w');

        if (!$file) {
            throw new \Exception(sprintf('Cannot open file %s', self::EXTENSION_FILE));
        }

        fwrite($file, $content);

        fclose($file);
    }

    private function createExtensionFile(): void
    {
        $this->writeWithStyle("Creating ", 'green');
        $this->writeWithStyle("config/extensions.php", 'basic');

        $this->writeln('');

        touch(self::EXTENSION_FILE);

        $file = fopen(self::EXTENSION_FILE, 'w');

        if (!$file) {
            throw new \Exception(sprintf('Cannot create file %s', self::EXTENSION_FILE));
        }

        fwrite($file, "<?php\n\nreturn [\n];");

        fclose($file);
    }
}
