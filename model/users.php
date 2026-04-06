<?php
// model/users.php

class users
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::StartUp();
    }

    /**
     * Login sin hashing — compara contraseña en texto plano.
     * Busca por username o email.
     *
     * @return array|false Datos del usuario (sin password) o false.
     */
    public function login(string $identifier, string $password): array |false
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, username, email, password, is_admin
                 FROM users
                 WHERE (username = ? OR email = ?)
                 LIMIT 1"
            );
            $stmt->execute([$identifier, $identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user)
                return false;
            if ($user['password'] !== $password)
                return false;

            unset($user['password']);
            return $user;

        }
        catch (PDOException $e) {
            error_log("users::login — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un usuario por ID (sin password).
     */
    public function getUserById(int $id): array |false
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, username, email, is_admin, created_at FROM users WHERE id = ?"
            );
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            error_log("users::getUserById — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea un usuario (solo para uso interno del admin, no desde el frontend).
     */
    public function create(string $username, string $email, string $password, bool $isAdmin = false): int|false
    {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                htmlspecialchars(strip_tags($username)),
                htmlspecialchars(strip_tags($email)),
                $password, // texto plano — sin hash
                $isAdmin ? 1 : 0,
            ]);
            return (int)$this->pdo->lastInsertId();
        }
        catch (PDOException $e) {
            error_log("users::create — " . $e->getMessage());
            if ($e->getCode() == 23000)
                throw new RuntimeException('El usuario o email ya existe.');
            return false;
        }
    }

    /**
     * Elimina un usuario.
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            return $stmt->execute([$id]);
        }
        catch (PDOException $e) {
            error_log("users::delete — " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lista todos los usuarios (sin passwords).
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT id, username, email, is_admin, created_at FROM users ORDER BY username ASC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            error_log("users::getAll — " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lista a todos los usuarios con la cantidad de libros subidos y total de likes recibidos.
     */
    public function getAllUsersWithStats(): array
    {
        try {
            $sql = "
SELECT 
    u.id, 
    u.username, 
    u.email, 
    u.is_admin,
    COUNT(DISTINCT b.id) AS books_count,
    COUNT(l.id) AS total_likes_received
FROM users u
LEFT JOIN books b ON u.id = b.uploaded_by
LEFT JOIN likes l ON b.id = l.book_id
GROUP BY u.id
ORDER BY u.id DESC;
            ";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e) {
            error_log("users::getAllUsersWithStats — " . $e->getMessage());
            return [];
        }
    }
}