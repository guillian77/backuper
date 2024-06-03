<?php

function fromCli():bool { return (php_sapi_name() === "cli"); };

function dd($to_debug): void
{
    if (!fromCli()) { echo "<pre class='debug'><code>"; }

    print_r($to_debug);

    if (!fromCli()) { echo "</code></pre>"; }

    die();
}

function dump($to_debug): void
{
    if (!fromCli()) { echo "<pre class='debug'><code>"; }

    print_r($to_debug);

    if (!fromCli()) { echo "</code></pre>"; }
}
