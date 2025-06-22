<?php
// registro.php

// Esto es importante para que SweetAlert pueda consumir el mensaje
if (session_status() == PHP_SESSION_NONE) session_start();

// La variable $csrf_token debe ser pasada desde el controlador.
// Si no está definida (ej. por acceso directo), se inicializa a vacío para evitar errores PHP.
$csrf_token = $csrf_token ?? ''; 
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../css/style.css"> <style>
        body {
            background: linear-gradient(135deg, #a7bfe8, #619af0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .register-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        .register-container h2 {
            color: #34495e;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        .btn-register {
            background-color: #2ecc71;
            border-color: #2ecc71;
            transition: background-color 0.3s ease;
        }
        .btn-register:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="register-container text-center">
        <h2 class="mb-4">Registro de Usuario</h2>
        <form action="index.php?c=account&a=register" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Nombre de Usuario" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Correo Electrónico" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirmar Contraseña" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
            <button type="submit" class="btn btn-register btn-lg w-100">Registrarse</button>
        </form>
        <p class="mt-4">¿Ya tienes una cuenta? <a href="index.php?c=account&a=loginForm">Inicia Sesión aquí</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>

    <?php
    // Manejador de SweetAlert directamente incrustado
    if (isset($_SESSION['sweet_alert'])) {
        $alert = $_SESSION['sweet_alert'];
        unset($_SESSION['sweet_alert']); // Consumir el mensaje
        $type = htmlspecialchars($alert['type']);
        $title = htmlspecialchars($alert['title']);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?php echo $type; ?>',
                title: '<?php echo $title; ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        });
    </script>
    <?php
    }
    ?>
</body>
</html>