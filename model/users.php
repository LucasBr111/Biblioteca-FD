<?php

class users
{
    private $pdo; 

    // Propiedades públicas para mapear con las columnas de la tabla 'users'
    public $id;
    public $username;
    public $email;
    public $password_hash; // Almacenará el hash de la contraseña
    public $is_admin;
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        try {
    
            $this->pdo = Database::StartUp();
        } catch (Exception $e) {
    
            error_log("Error en la conexión a la BD para el modelo User: " . $e->getMessage());
            die("Error en la conexión a la base de datos."); 
        }
    }


    public function register(string $username, string $email, string $password, bool $is_admin = false)
    {
        try {
            // Hashing de la contraseña para seguridad (IMPRESCINDIBLE)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (username, email, password_hash, is_admin) VALUES (?, ?, ?, ?)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                htmlspecialchars(strip_tags($username)),
                htmlspecialchars(strip_tags($email)),    
                $hashed_password,
                $is_admin
            ]);

            return $this->pdo->lastInsertId(); 
        } catch (PDOException $e) {
    
            error_log("Error al registrar usuario: " . $e->getMessage());
          
            if ($e->getCode() == 23000) { 
                throw new Exception("El nombre de usuario o correo electrónico ya está en uso.");
            }
            return false;
        }
    }

  
    public function login(string $username_or_email, string $password)
    {
        try {

            $sql = "SELECT id, username, email, password_hash, is_admin FROM users WHERE username = ? OR email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username_or_email, $username_or_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC); 
          
            if ($user && password_verify($password, $user['password_hash'])) {
                
                // Opcional: Re-hashear la contraseña si el algoritmo o costo necesita actualización
                // Esto mejora la seguridad con el tiempo sin forzar al usuario a cambiar la contraseña.
                if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT)) {
                    $new_hash = password_hash($password, PASSWORD_BCRYPT);
                    $this->updatePasswordHash($user['id'], $new_hash);
                }
                
               
                unset($user['password_hash']); 
                return $user;
            } else {
              
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error en el login: " . $e->getMessage());
            return false;
        }
    }

 
    public function getUserById(int $id)
    {
        try {
            $sql = "SELECT id, username, email, is_admin, created_at, updated_at FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por ID: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByUsername(string $username)
    {
        try {
            $sql = "SELECT id, username, email, is_admin, created_at, updated_at FROM users WHERE username = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por username: " . $e->getMessage());
            return false;
        }
    }

 
    public function getUserByEmail(string $email)
    {
        try {
            $sql = "SELECT id, username, email, is_admin, created_at, updated_at FROM users WHERE email = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios. Útil para una sección de administración.
     * IMPORTANTE: No selecciona el campo 'password_hash' por seguridad.
     * @return array Un array de arrays asociativos con los datos de los usuarios.
     */
    public function getAllUsers(): array
    {
        try {
            $sql = "SELECT id, username, email, is_admin, created_at, updated_at FROM users ORDER BY username ASC"; 
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return [];
        }
    }


    public function update(int $id, string $username, string $email, bool $is_admin): bool
    {
        try {
            $sql = "UPDATE users SET username = ?, email = ?, is_admin = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                htmlspecialchars(strip_tags($username)),
                htmlspecialchars(strip_tags($email)),
                $is_admin,
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario ID " . $id . ": " . $e->getMessage());
            // Manejo específico para violación de restricción UNIQUE
            if ($e->getCode() == 23000) {
                throw new Exception("El nombre de usuario o correo electrónico ya está en uso por otro usuario.");
            }
            return false;
        }
    }

    private function updatePasswordHash(int $userId, string $newHash): bool
    {
        try {
            $sql = "UPDATE users SET password_hash = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$newHash, $userId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar hash de contraseña para el usuario " . $userId . ": " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario ID " . $id . ": " . $e->getMessage());
            // Manejar si hay restricciones de clave foránea (ej., un libro referenciando a este usuario)
            if ($e->getCode() == 23000) { 
                 throw new Exception("No se puede eliminar el usuario porque tiene libros asociados u otras dependencias.");
            }
            return false;
        }
    }
}