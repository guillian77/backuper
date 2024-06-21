<?php

use App\Service\RequestService;
use Src\App;

require "plugins/backuper/app/App.php";

App::get()->boot("plugins/backuper");

$request = new RequestService();
$controller = $request->post("controller");
$method = $request->post("method", "index");
$data = $request->post("data", []);

$namespace = "App\\Controller\\$controller";

$instance = new $namespace();
$jsonResponse = call_user_func([$instance, $method], ...$data);

echo $jsonResponse;
