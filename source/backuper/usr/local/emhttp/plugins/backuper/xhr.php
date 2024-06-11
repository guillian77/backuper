<?php

use App\App;
use app\service\RequestService;

require "plugins/backuper/app/App.php";

App::get()->boot("plugins/backuper");

$request = new RequestService();
$controller = $request->post("controller");
$method = $request->post("method", "index");
$data = $request->post("data", []);

$namespace = "app\\controller\\$controller";

$instance = new $namespace();
$jsonResponse = call_user_func([$instance, $method], ...$data);

echo $jsonResponse;
