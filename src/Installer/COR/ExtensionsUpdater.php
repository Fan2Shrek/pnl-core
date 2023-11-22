<?php

namespace Pnl\Installer\COR;

use Pnl\Installer\PnlConfig;

class ExtensionsUpdater extends AbsractInstaller
{
    private const EXTENSION_FILE = __DIR__ . '/../../../config/extensions.php';

    public function install(PnlConfig $pnlConfig): PnlConfig
    {
        $this->style->writeWithStyle("Updating ", 'green');
        $this->style->writeWithStyle("config/extensions.php...", 'basic');

        $this->style->writeln('');

        if (!file_exists(self::EXTENSION_FILE)) {
            $this->createExtensionFile();
        }

        $this->updateExtensionFile($pnlConfig->mainClass);

        $this->style->writeWithStyle("✅ Done", 'green');
        $this->style->writeln('');
        $this->style->writeln('');

        return $pnlConfig;
    }

    private function updateExtensionFile(string $add): void
    {
        $extensions = require self::EXTENSION_FILE;
        $extensions[] = $add;

        $content = "<?php\n\nreturn [\n";

        foreach ($extensions as $extension) {
            $content .= "\t$extension::class,\n";
        }

        $content .= '];';

        $file = fopen(self::EXTENSION_FILE, 'w');
        fwrite($file, $content);

        fclose($file);
    }

    private function createExtensionFile(): void
    {
        $this->style->writeWithStyle("Creating ", 'green');
        $this->style->writeWithStyle("config/extensions.php", 'basic');

        $this->style->writeln('');

        touch(self::EXTENSION_FILE);

        $file = fopen(self::EXTENSION_FILE, 'w');

        fwrite($file, "<?php\n\nreturn [\n];");

        fclose($file);
    }
}