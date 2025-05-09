<?php

namespace app\service;

class FileBrowserService
{
    const ROOT = "/root/..";

    /**
     * Return directory list inside a given path.
     *
     * @param string $target
     *
     * @return array|null
     */
    public function scan(string $target): ?array
    {
        // Set linux root by default.
        if ($target === "") { $target = self::ROOT; }

        // Avoid non-existing path.
        if (!file_exists($target)) { return null; }

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

        $dirs['parent'] = dirname($target);

        return $dirs;
    }

    private function plugPath(string $dirname, string $path): string
    {
        if ($path === self::ROOT) { $path = ""; }

        if ($dirname === "..") { return $dirname; }

        $entirePath = $path . "/". $dirname;

        return str_replace("//", "/", $entirePath);
    }
}
