<?php

require "plugins/backuper/app/App.php";

\App\App::get()->boot("plugins/backuper");

new \App\Service\BackupService();
