<?php
require_once "model/database.php";

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si estamos en la URL base
$currentUrl = $_SERVER['REQUEST_URI'];
$baseUrl = '/fd/';

if ($currentUrl === $baseUrl || $currentUrl === '/fd/index.php') {
    header('Location: /fd/index.php?c=main');
    exit();
}

// Resto del código existente
$controller = isset($_GET['c']) ? strtolower($_GET['c']) : 'main';
$action = isset($_GET['a']) ? strtolower($_GET['a']) : 'index';

// Validar que el controlador exista
$controllerFile = "controller/" . $controller . "Controller.php";

if (!file_exists($controllerFile)) {
    include_once 'view/error/404.php';
    exit();
}

require_once $controllerFile;
$controllerClass = ucfirst($controller) . "Controller";

if (!class_exists($controllerClass)) {
    include_once 'view/error/404.php';
    exit();
}

$controllerInstance = new $controllerClass();

if (!method_exists($controllerInstance, $action)) {
    include_once 'view/error/404.php';
    exit();
}

// Ejecutar la acción
$controllerInstance->$action();