<?php
// controller/accountController.php

require_once 'model/Database.php';
require_once 'model/users.php';

class accountController
{
    private users $userModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        $this->userModel = new users();
    }

    private function alert(string $type, string $title): void
    {
        $_SESSION['sweet_alert'] = ['type' => $type, 'title' => $title];
    }

    private function redirect(string $controller, string $action = 'index'): void
    {
        $url = "index.php?c={$controller}";
        if ($action !== 'index')
            $url .= "&a={$action}";
        header("Location: {$url}");
        exit();
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $path = __DIR__ . '/../view/account/' . $view . '.php';
        if (!file_exists($path))
            die("Vista no encontrada: {$view}");
        include $path;
    }

    // ── Actions ───────────────────────────────────────────────

    /**
     * Formulario de login.
     */
    public function loginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('main');
        }
        $this->render('login');
    }

    /**
     * Procesa el login.
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('account', 'loginForm');
        }

        $identifier = trim($_POST['username_or_email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$identifier || !$password) {
            $this->alert('warning', 'Por favor, ingresá tu usuario y contraseña.');
            $this->redirect('account', 'loginForm');
        }

        $user = $this->userModel->login($identifier, $password);

        if (!$user) {
            // Un mensaje estándar por seguridad (no revelar si falló el usuario o la clave)
            $this->alert('error', 'Las credenciales ingresadas no son correctas.');
            $this->redirect('account', 'loginForm');
        }

        // Iniciar sesión
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = (bool)$user['is_admin'];

        $this->alert('success', '¡Bienvenido, ' . htmlspecialchars($user['username']) . '!');
        $this->redirect('main');
    }

    /**
     * Formulario de registro.
     */
    public function registerForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('main');
        }
        $this->render('registro');
    }

    /**
     * Procesa el registro.
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('account', 'registerForm');
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // 1. Validación de campos vacíos
        if (!$username || !$email || !$password || !$confirm) {
            $this->alert('warning', 'Debes completar todos los campos del formulario.');
            $this->redirect('account', 'registerForm');
        }

        // 2. Validación de formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->alert('warning', 'El formato del correo electrónico no es válido.');
            $this->redirect('account', 'registerForm');
        }

        // 3. Validación de longitud de usuario
        if (strlen($username) < 3 || strlen($username) > 20) {
            $this->alert('warning', 'El usuario debe tener entre 3 y 20 caracteres.');
            $this->redirect('account', 'registerForm');
        }

        // 4. Validación de longitud de contraseña
        if (strlen($password) < 6) {
            $this->alert('warning', 'La contraseña es muy corta. Usa al menos 6 caracteres.');
            $this->redirect('account', 'registerForm');
        }

        // 5. Validación de coincidencia de contraseñas
        if ($password !== $confirm) {
            $this->alert('error', 'Las contraseñas no coinciden. Verifícalas e inténtalo de nuevo.');
            $this->redirect('account', 'registerForm');
        }

        try {
            $userId = $this->userModel->create($username, $email, $password);

            if (!$userId) {
                $this->alert('error', 'No se pudo generar tu cuenta. Intenta de nuevo más tarde.');
                $this->redirect('account', 'registerForm');
            }

            // Iniciar sesión automáticamente
            $user = $this->userModel->login($username, $password);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];

                $this->alert('success', '¡Cuenta creada con éxito! Bienvenido, ' . htmlspecialchars($user['username']) . '.');
                $this->redirect('main');
            }
            else {
                $this->alert('success', '¡Cuenta creada con éxito! Por favor inicia sesión.');
                $this->redirect('account', 'loginForm');
            }

        }
        catch (RuntimeException $e) {
            // AQUI ESTÁ LA MAGIA: Pasamos el mensaje exacto del modelo ("El usuario o email ya existe.")
            $this->alert('error', $e->getMessage());
            $this->redirect('account', 'registerForm');
        }
        catch (Exception $e) {
            $this->alert('error', 'Error del servidor. Por favor, intenta más tarde.');
            $this->redirect('account', 'registerForm');
        }
    }

    /**
     * Panel de administrador mejorado
     */
    public function adminDashboard(): void
    {
        if (empty($_SESSION['is_admin'])) {
            $this->alert('error', 'Acceso denegado. Se requieren permisos de administrador.');
            $this->redirect('main');
        }

        require_once 'model/books.php';
        $bookModel = new books();

        $stats = $bookModel->getGeneralStats();
        $allBooks = $bookModel->getAllBooksAdmin();
        $allUsers = $this->userModel->getAllUsersWithStats();

    

        $this->render('admin_dashboard', [
            'stats' => $stats,
            'books' => $allBooks,
            'users' => $allUsers
        ]);
    }

    /**
     * Cierra la sesión.
     */
    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
        session_start();
        $this->alert('success', 'Sesión cerrada correctamente. ¡Hasta pronto!');
        $this->redirect('main');
    }
}