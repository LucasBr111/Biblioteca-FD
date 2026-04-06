<?php
// model/books.php

class books
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::StartUp();
    }

    // ── Reads ─────────────────────────────────────────────────

    /**
     * Todos los libros publicados con conteo de likes.
     * Si se pasa $userId, indica si el usuario ya dio like.
     */
    public function getAllPublishedBooks(?int $userId = null): array
    {
        try {
            $sql = "
                SELECT b.*,
                       COUNT(DISTINCT l.id) AS likes,
                       MAX(u.email) AS uploader_email,
                       MAX(u.username) AS uploader_username
                       " . ($userId ? ", MAX(CASE WHEN l.user_id = :uid THEN 1 ELSE 0 END) AS user_liked" : ", 0 AS user_liked") . "
                FROM books b
                LEFT JOIN likes l ON l.book_id = b.id
                LEFT JOIN users u ON b.uploaded_by = u.id
                WHERE b.publicado = 'si'
                GROUP BY b.id
                ORDER BY b.id DESC
            ";
            $stmt = $this->pdo->prepare($sql);
            if ($userId) $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getAllPublishedBooks: " . $e->getMessage());
            return [];
        }
    }

    public function getAllBooks(?int $userId = null): array
    {
        return $this->getAllPublishedBooks($userId);
    }

    /**
     * Solicitudes pendientes de publicación.
     */
    public function getAllPulls(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT b.*, u.email AS uploader_email FROM books b LEFT JOIN users u ON b.uploaded_by = u.id WHERE b.publicado = 'no' ORDER BY b.id DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getAllPulls: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Libro por ID.
     */
    public function getBookById(int $id): array|false
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM books WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("getBookById: " . $e->getMessage());
            return false;
        }
    }

    // ── Writes ────────────────────────────────────────────────

    /**
     * Inserta un libro con publicado = 'no'.
     */
    public function uploadBookPull(array $data): int|false
    {
        try {
            // Verificar título duplicado
            $check = $this->pdo->prepare("SELECT id FROM books WHERE title = ?");
            $check->execute([$data['title']]);
            if ($check->fetch()) throw new RuntimeException("Ya existe un libro con ese título.");

            $stmt = $this->pdo->prepare("
                INSERT INTO books
                  (title, author, year, genre, description, image_path, pdf_path, url_resumen, publicado, uploaded_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'no', ?)
            ");
            $stmt->execute([
                $data['title'],
                $data['author'],
                $data['year'],
                $data['genre'],
                $data['description'],
                $data['cover_image_path'],
                $data['pdf_path'],
                $data['url_resumen'] ?? null,
                $data['uploaded_by'] ?? null,
            ]);
            return (int)$this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("uploadBookPull: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Publica un libro (cambia publicado a 'si').
     */
    public function publishBook(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("UPDATE books SET publicado = 'si' WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("publishBook: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un libro.
     */
    public function deleteBook(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("deleteBook: " . $e->getMessage());
            return false;
        }
    }

    // ── Likes ─────────────────────────────────────────────────

    /**
     * Alterna el like de un usuario sobre un libro.
     * Retorna ['liked' => bool, 'count' => int].
     */
    public function toggleLike(int $userId, int $bookId): array
    {
        try {
            // Verificar si ya existe el like
            $check = $this->pdo->prepare(
                "SELECT id FROM likes WHERE user_id = ? AND book_id = ?"
            );
            $check->execute([$userId, $bookId]);
            $existing = $check->fetch();

            if ($existing) {
                // Quitar like
                $del = $this->pdo->prepare("DELETE FROM likes WHERE user_id = ? AND book_id = ?");
                $del->execute([$userId, $bookId]);
                $liked = false;
            } else {
                // Dar like
                $ins = $this->pdo->prepare("INSERT INTO likes (user_id, book_id) VALUES (?, ?)");
                $ins->execute([$userId, $bookId]);
                $liked = true;
            }

            // Contar likes actuales
            $count = $this->getLikeCount($bookId);
            return ['liked' => $liked, 'count' => $count];

        } catch (PDOException $e) {
            error_log("toggleLike: " . $e->getMessage());
            return ['liked' => false, 'count' => 0];
        }
    }

    /**
     * Cuenta los likes de un libro.
     */
    public function getLikeCount(int $bookId): int
    {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM likes WHERE book_id = ?");
            $stmt->execute([$bookId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("getLikeCount: " . $e->getMessage());
            return 0;
        }
    }
}