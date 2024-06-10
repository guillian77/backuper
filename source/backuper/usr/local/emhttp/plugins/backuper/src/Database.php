<?php

namespace src;

use Exception;
use SQLite3;

class Database
{
    public SQLite3 $conn;

    public function __construct(string $db_file)
    {
        if (!file_exists($db_file)) {
            file_put_contents($db_file, "");
        }

        $this->conn = new SQLite3($db_file);
    }
}
