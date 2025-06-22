<?php
require_once "model/Database.php";
require_once "model/users.php";
require_once "model/books.php";



class mainController {

    private function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    private $books;
    public function __construct() {
        $this->books = new books();
    }

    public function index() {
        $libros =  $this->books->getAllPublishedBooks();
        require_once "view/main.php";
    }
}