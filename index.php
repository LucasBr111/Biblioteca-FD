<?php
// index.php — Front controller

require_once 'model/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir raíz
if (empty($_GET['c']) && empty($_GET['a'])) {
    header('Location: index.php?c=main');
    exit();
}

$controller = strtolower($_GET['c'] ?? 'main');
$action     = strtolower($_GET['a'] ?? 'index');

// Permitir solo caracteres seguros
if (!preg_match('/^[a-z]+$/', $controller) || !preg_match('/^[a-z]+$/', $action)) {
    http_response_code(404);
    include 'view/error/404.php';
    exit();
}

$controllerFile  = "controller/{$controller}Controller.php";
$controllerClass = ucfirst($controller) . 'Controller';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    include 'view/error/404.php';
    exit();
}

require_once $controllerFile;

if (!class_exists($controllerClass)) {
    http_response_code(404);
    include 'view/error/404.php';
    exit();
}

$instance = new $controllerClass();

if (!method_exists($instance, $action)) {
    http_response_code(404);
    include 'view/error/404.php';
    exit();
}

$instance->$action();