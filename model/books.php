<?php
class books
{
    private $pdo;
    public $books;
    public $title;
    public $author;
    public $year;
    public $genre;
    public $description;
    public $cover_image_path;
    public $pdf_path;
    public $url_resumen;

    public function __construct()
    {
        // Conexión a la base de datos
        // Asegúrate de que Database::StartUp() devuelva una instancia de PDO
        $this->pdo = Database::StartUp();
    }

    /**
     * Inserta un nuevo libro en la base de datos como una solicitud (pull).
     * Por defecto, el libro NO estará publicado.
     *
     * @param array $data Datos del libro: title, author, year, genre, description, cover_image_path, pdf_path, summary_url
     * @return int|false El ID del libro insertado o false en caso de error.
     * @throws Exception Si el título o PDF ya existen, o si hay un error de DB.
     */
    public function uploadBookPull(array $data)
    {
        try {
            // Añadir logging
            error_log("Intentando subir libro: " . print_r($data, true));
            
            $stmt = $this->pdo->prepare("SELECT id FROM books WHERE title = ?");
            $stmt->execute([$data['title']]);
            
            if ($stmt->fetch()) {
                throw new Exception("Ya existe un libro con este título.");
            }

            $sql = "INSERT INTO books (
                title, author, year, genre, description, 
                image_path, pdf_path, url_resumen, publicado
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'no')";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $data['title'],
                $data['author'],
                $data['year'],
                $data['genre'],
                $data['description'],
                $data['cover_image_path'],
                $data['pdf_path'],
                $data['url_resumen'] ?? null
            ]);

            if (!$result) {
                error_log("Error al insertar libro: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Error al insertar el libro en la base de datos");
            }

            return $this->pdo->lastInsertId();

        } catch (PDOException $e) {
            error_log("Error PDO en uploadBookPull: " . $e->getMessage());
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene todos los libros marcados como 'no' publicados (los pulls pendientes).
     *
     * @return array Una lista de libros.
     */
    public function getAllPulls()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE publicado = 'no' ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getAllPulls: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene todos los libros marcados como 'si' publicados.
     *
     * @return array Una lista de libros.
     */
    public function getAllPublishedBooks()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE publicado = 'si' ORDER BY id DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getAllPublishedBooks: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualiza el estado de un libro a 'publicado' ('si').
     *
     * @param int $bookId El ID del libro a publicar.
     * @return bool True si se actualizó, false en caso contrario.
     */
    public function publishBook(int $bookId)
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE books SET publicado = 'si' WHERE id = ?");
            return $stmt->execute([$bookId]);
        } catch (PDOException $e) {
            error_log("Database error in publishBook: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un libro de la base de datos.
     * NO elimina los archivos, eso debe ser manejado en el controlador.
     *
     * @param int $bookId El ID del libro a eliminar.
     * @return bool True si se eliminó, false en caso contrario.
     */
    public function deleteBook(int $bookId)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = ?");
            return $stmt->execute([$bookId]);
        } catch (PDOException $e) {
            error_log("Database error in deleteBook: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene la información de un libro por su ID.
     *
     * @param int $bookId El ID del libro.
     * @return array|false Los datos del libro o false si no se encuentra.
     */
    public function getBookById(int $bookId)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = ?");
            $stmt->execute([$bookId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getBookById: " . $e->getMessage());
            return false;
        }
    }

    // Puedes añadir aquí el método para obtener todos los libros para la vista principal
    // (el que usabas antes del cambio de 'publicado')
    public function getAllBooks()
    {
        return $this->getAllPublishedBooks(); // Ahora getAllBooks solo devuelve los publicados
    }
}