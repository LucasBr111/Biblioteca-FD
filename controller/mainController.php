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
        $libros =  $this->books->getAllPublishedBooks();
        require_once "view/main.php";
    }
}