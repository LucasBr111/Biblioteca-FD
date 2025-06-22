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
     * Genera un nuevo token CSRF si no existe en la sesión y lo devuelve.
     * @return string El token CSRF actual.
     */
    private function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }


    /**
     * Valida el token CSRF recibido en una solicitud.
     * Consume el token para evitar ataques de doble envío.
     * @param string $token El token recibido del formulario o URL.
     * @return bool True si el token es válido, false en caso contrario.
     */
    private function validateCSRFToken($token)
    {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            return false;
        }
        // Consume el token para que no se reutilice en subsiguientes envíos
        unset($_SESSION['csrf_token']); 
        return true;
    }

    /**
     * Muestra el formulario de registro.
     */
    public function registerForm()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?c=main');
            exit();
        }
         $this->render('registro'); // Asumiendo que tienes una vista 'register.php'
    }

    /**
     * Procesa el registro de un nuevo usuario.
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showMessageAndRedirect('error', 'Método de solicitud no permitido.', 'account', 'registerForm', ''); 
            return;
        }

        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->showMessageAndRedirect('error', 'Error de seguridad: Token CSRF inválido. Por favor, recarga la página e inténtalo de nuevo.', 'account', 'registerForm', 'Token inválido'); 
            return;
        }

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
        $this->render('login', ['csrf_token' => $this->generateCSRFToken()]);
    }

    /**
     * Procesa el login de un usuario.
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showMessageAndRedirect('error', 'Método de solicitud no permitido.', 'account', 'loginForm', ''); 
            return;
        }

        if (!$this->validateCSRFToken($_POST['csrf_token'] ?? '')) {
            $this->showMessageAndRedirect('error', 'Error de seguridad: Token CSRF inválido. Por favor, recarga la página e inténtalo de nuevo.', 'account', 'loginForm', 'Token inválido'); 
            return;
        }

    

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
        session_unset();   
        session_destroy();


//elimina la cookie de sesión si se está utilizando
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        $this->showMessageAndRedirect('success', 'Has cerrado sesión correctamente.', 'main');
    }

    /**
     * Renderiza una vista y pasa datos a ella, incluyendo el token CSRF.
     * @param string $viewName El nombre de la vista (sin .php).
     * @param array $data Un array asociativo de datos a pasar a la vista.
     */

    private function render(string $viewName, array $data = [])
    {
        
        extract($data);

        if (!isset($csrf_token)) {
            $csrf_token = $this->generateCSRFToken();
        }

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
     * @param string $text      Texto adicional para el mensaje de SweetAlert (opcional)
     */
    private function showMessageAndRedirect(string $type, string $title, string $controller, string $text = '', string $action = 'index')
    {
        $_SESSION['sweet_alert'] = [
            'type' => $type,
            'title' => $title,
            'text' => $text // Puedes añadir un campo 'text' si quieres mensajes más largos
        ];

        $location = "index.php?c={$controller}";
        if ($action !== 'index') { // Solo añade la acción si no es la por defecto
            $location .= "&a={$action}";
        }
        header("Location: {$location}");
        exit();
    }
}