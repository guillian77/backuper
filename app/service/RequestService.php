<?php

namespace App\Service;

class RequestService
{
    public function post($key, $default = null)
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }

        return $default;
    }
}
