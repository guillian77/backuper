<?php

namespace app\service;

class RequestService
{

    // TODO: Securize data from client.

    public function get($key, $default = null)
    {
        if (!isset($_GET[$key])) {
            return $default;
        }

        return $_GET[$key];
    }

    public function post($key, $default = null)
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        return $_POST[$key];
    }

    /**
     * HTML Redirection to a target page.
     *
     * Trick redirection because Unraid already set HTTP Headers.
     *
     * @param string $url target page.
     *
     * @return void
     */
    public function redirect(string $url): void
    {
        echo "<meta http-equiv='refresh' content='0; URL=$url'>";
    }
}
