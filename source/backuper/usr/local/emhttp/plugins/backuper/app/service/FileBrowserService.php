<?php

namespace app\service;

class FileBrowserService
{
    const ROOT = "/root/..";

    private ?string $previous = null;

    public function scan(string $target, string $previous): ?array
    {
        // Set linux root by default.
        if ($target === "") { $target = self::ROOT; }

        // Avoid non-existing path.
        if (!file_exists($target)) { return null; }

        if ($previous !== "") { $target = dirname($previous); }

        $dirNames = scandir($target);

        $dirs = [];
        foreach ($dirNames as $dirname) {
            // Add full path to dirname.
            $path = $this->plugPath($dirname, $target);

            // Avoid current dir linux symbol.
            if ($dirname === ".") { continue; }

            // Only return directory.
            if (!is_dir($path)) { continue; }

            $dirs[] = $path;
        }

        return $dirs;
    }

    private function isSymbols(string $dirname): bool
    {
        return in_array($dirname, [".", ".."]);
    }

    private function plugPath(string $dirname, string $path): string
    {
        if ($path === self::ROOT) { $path = ""; }

        if ($dirname === "..") { return $dirname; }

        $entirePath = $path . "/". $dirname;

        return str_replace("//", "/", $entirePath);
    }
}
