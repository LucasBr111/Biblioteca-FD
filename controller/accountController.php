<?php
// app/controllers/AccountController.php

// Asegúrate de incluir el modelo User y el modelo Database
// La ruta puede variar según la estructura de tu proyecto.
require_once 'model/Database.php'; // Para asegurar que Database::StartUp() esté disponible
require_once 'model/users.php'; // Tu modelo de usuario

// Para mensajes de feedback al usuario
// require_once 'app/helpers/FlashMessage.php'; // Si tienes un sistema de mensajes flash, lo usarías aquí.

class AccountController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new users();
    }

    /**
     * Muestra el formulario de registro.
     */
    public function registerForm()
    {
        // No hay necesidad de pasar datos específicos a la vista en este caso.
        // Solo renderizar el formulario.
        $this->render('registro'); // Asumiendo que tienes una vista 'register.php'
    }

    /**
     * Procesa el registro de un nuevo usuario.
     */
    public function register()
    {
        session_start(); // Asegura que la sesión esté iniciada para el token CSRF y mensa

        // 2. Validar datos de entrada
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $this->showMessageAndRedirect('error', 'Todos los campos son obligatorios.', 'account', 'registerForm');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showMessageAndRedirect('error', 'Formato de correo electrónico inválido.', 'account', 'registerForm');
            return;
        }

        if (strlen($username) < 3 || strlen($username) > 50) {
            $this->showMessageAndRedirect('error', 'El nombre de usuario debe tener entre 3 y 50 caracteres.', 'account', 'registerForm');
            return;
        }

        if (strlen($password) < 6) {
            $this->showMessageAndRedirect('error', 'La contraseña debe tener al menos 6 caracteres.', 'account', 'registerForm');
            return;
        }

        if ($password !== $confirmPassword) {
            $this->showMessageAndRedirect('error', 'Las contraseñas no coinciden.', 'account', 'registerForm');
            return;
        }

        try {
            // 3. Intentar registrar al usuario
            // Por defecto, un usuario registrado NO es administrador
            $newUserId = $this->userModel->register($username, $email, $password, false);

            if ($newUserId) {
                // Registro exitoso, iniciar sesión automáticamente
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = false; // El nuevo usuario no es admin por defecto
                session_regenerate_id(true); // Regenerar ID de sesión por seguridad

                $this->showMessageAndRedirect('success', '¡Registro exitoso! Bienvenido.', 'main'); // Redirigir al home
            } else {
                // Esto podría ocurrir si hay un error en la base de datos no capturado por el try-catch interno del modelo
                $this->showMessageAndRedirect('error', 'Error al registrar el usuario. Inténtalo de nuevo.', 'account', 'registerForm');
            }
        } catch (Exception $e) {
            // Capturar excepciones lanzadas por el modelo (ej. usuario/email duplicado)
            $this->showMessageAndRedirect('error', $e->getMessage(), 'account', 'registerForm');
        }
    }

    /**
     * Muestra el formulario de login.
     */
    public function loginForm()
    {
        // Si el usuario ya está logueado, redirigir al home
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?c=main');
            exit();
        }
        $this->render('login'); // Asumiendo que tienes una vista 'login.php'
    }

    /**
     * Procesa el login de un usuario.
     */
    public function login()
    {
        session_start(); // Asegura que la sesión esté iniciada para el token CSRF y mensajes

    

        // 2. Validar datos de entrada
        $usernameOrEmail = trim($_POST['username_or_email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($usernameOrEmail) || empty($password)) {
            $this->showMessageAndRedirect('error', 'Por favor, introduce tu nombre de usuario/email y contraseña.', 'account', 'loginForm');
            return;
        }

        // 3. Intentar iniciar sesión
        $userData = $this->userModel->login($usernameOrEmail, $password);

        if ($userData) {
            // Login exitoso, guardar datos en sesión
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['is_admin'] = (bool)$userData['is_admin']; // Asegúrate de guardar un booleano
            session_regenerate_id(true); // Regenerar ID de sesión por seguridad

            $this->showMessageAndRedirect('success', '¡Bienvenido de nuevo, ' . htmlspecialchars($userData['username']) . '!', 'main');
        } else {
            // Credenciales inválidas
            $this->showMessageAndRedirect('error', 'Nombre de usuario/email o contraseña incorrectos.', 'account', 'loginForm');
        }
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout()
    {
        session_start(); // Asegura que la sesión esté iniciada
        session_unset();   // Elimina todas las variables de sesión
        session_destroy(); // Destruye la sesión en el servidor

        // Opcional: Eliminar la cookie de sesión
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        $this->showMessageAndRedirect('success', 'Has cerrado sesión correctamente.', 'main');
    }

    private function render(string $viewName, array $data = [])
    {
        // Extrae los datos para que estén disponibles como variables en la vista
        extract($data); 

        // Genera un nuevo token CSRF para el formulario que se va a renderizar
        // Esto es importante para que cada formulario tenga un token fresco
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        $csrf_token = $_SESSION['csrf_token']; // Pasa el token a la vista

        // Incluye el archivo de la vista
        $viewPath = __DIR__ . '/../view/account/' . $viewName . '.php'; // Ajusta la ruta si es necesario

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // Manejo de error si la vista no existe
            die("Error: Vista '{$viewName}.php' no encontrada en '{$viewPath}'");
        }
    }

    /**
     * Muestra un mensaje SweetAlert y redirige.
     * Esto requiere que tu layout (main.php) tenga SweetAlert2 incluido.
     * Los mensajes se guardan en sesión y se consumen una vez.
     *
     * @param string $type  Tipo de SweetAlert (success, error, warning, info, question)
     * @param string $title Título del mensaje
     * @param string $controller Controlador al que redirigir
     * @param string $action    Acción del controlador al que redirigir (opcional)
     */
    private function showMessageAndRedirect(string $type, string $title, string $controller, string $action = 'index')
    {
        $_SESSION['sweet_alert'] = [
            'type' => $type,
            'title' => $title,
            // 'text' => $text // Puedes añadir un campo 'text' si quieres mensajes más largos
        ];

        $location = "index.php?c={$controller}";
        if ($action !== 'index') { // Solo añade la acción si no es la por defecto
            $location .= "&a={$action}";
        }
        header("Location: {$location}");
        exit();
    }
}