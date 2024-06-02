<?php

function dd($to_debug)
{
    echo "<pre class='debug'><code>";
    print_r($to_debug);
    echo "</code></pre>";
    die();
}

function dump($to_debug)
{
    echo "<pre class='debug'><code>";
    print_r($to_debug);
    echo "</code></pre>";
}
