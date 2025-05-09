<?php

if(!function_exists("readline")) {
    function readline($prompt = null, string $default = null) {
        if ($prompt) { echo "$prompt $default"; }

        $fp = fopen("php://stdin","r");

        return rtrim(fgets($fp, 1024));
    }
}

function fromCli():bool { return (php_sapi_name() === "cli"); };

function spacer()
{
    if (!fromCli()) { echo "<hr/>"; }
    if (fromCli()) { echo "---------------------------------------"; }
    if (fromCli()) { echo "\n"; }
}
