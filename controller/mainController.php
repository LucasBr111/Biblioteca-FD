<?php
require_once "model/Database.php";
require_once "model/users.php";
require_once "model/books.php";



class mainController {

    private $books;
    public function __construct() {
        $this->books = new books();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? null;
        $libros =  $this->books->getAllPublishedBooks($userId);
        require_once "view/main.php";
    }
}