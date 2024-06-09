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
}
