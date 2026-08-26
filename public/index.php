<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/TaskController.php';
require_once __DIR__ . '/../app/models/Task.php';

$controllerName = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

if ($controllerName === 'task') {
    $controller = new TaskController();
} else {
    $controller = new HomeController();
}

if (!method_exists($controller, $action)) {
    http_response_code(404);
    exit('Page introuvable');
}

$controller->{$action}();
