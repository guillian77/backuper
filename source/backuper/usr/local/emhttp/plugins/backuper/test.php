<?php
require "plugins/backuper/app/App.php";
\App\App::get()->boot("plugins/backuper");

$history = new \App\Entity\History();
$history->getDirs();