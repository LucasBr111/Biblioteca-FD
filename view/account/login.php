<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <!-- <style>
        body {
            background: linear-gradient(135deg, #a7bfe8, #619af0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }
        .login-container h2 {
            color: #34495e;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }
        .btn-login {
            background-color: #3498db;
            border-color: #3498db;
            transition: background-color 0.3s ease;
        }
        .btn-login:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
    </style> -->
    <style>
        :root {
    --color1: #0c577c;
    --color2: #09486c;
    --color3: #063a5c;
    --color4: #032b4b;
    --color5: #001c3b;

    --text-light: #e0e0e0;
    --text-dark: #333;
    --background-light: #f8f9fa;
    --background-dark: var(--color5);
    --transition: all 0.3s ease;
    --shadow-light: rgba(0, 0, 0, 0.08);
    --shadow-dark: rgba(0, 0, 0, 0.2);
}

body {
    background: linear-gradient(135deg, var(--color3), var(--color5));
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    color: var(--text-dark);
}

.login-container {
    background-color: rgba(255, 255, 255, 0.98);
    padding: 3rem;
    border-radius: 1.5rem;
    box-shadow: 0 15px 40px var(--shadow-dark);
    max-width: 450px;
    width: 100%;
    text-align: center;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.login-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px var(--shadow-dark);
}

.login-container h2 {
    color: var(--color1);
    margin-bottom: 2rem;
    font-weight: 700;
    font-size: 2.2rem;
    letter-spacing: 0.5px;
    position: relative;
    padding-bottom: 10px;
}

.login-container h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background-color: var(--color2);
    border-radius: 2px;
}

.form-control {
    border: 1px solid var(--color4);
    padding: 0.85rem 1.25rem;
    border-radius: 0.75rem;
    font-size: 1.05rem;
    color: var(--text-dark);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    border-color: var(--color1);
    /* Necesitarías definir --color1-rgb para que esto funcione, por ejemplo: --color1-rgb: 12, 87, 124; */
    box-shadow: 0 0 0 0.25rem rgba(12, 87, 124, 0.25);
}

.btn-login {
    background-color: var(--color1);
    border-color: var(--color1);
    color: var(--text-light);
    padding: 1rem 2.5rem;
    font-size: 1.15rem;
    font-weight: 600;
    border-radius: 0.75rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 1.5rem;
}

.btn-login:hover {
    background-color: var(--color2);
    border-color: var(--color2);
    transform: translateY(-3px);
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
}

.btn-login:active {
    background-color: var(--color3);
    border-color: var(--color3);
    transform: translateY(0);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.login-links {
    margin-top: 1.5rem;
    font-size: 0.95rem;
}

.login-links a {
    color: var(--color3);
    text-decoration: none;
    transition: color 0.3s ease;
}

.login-links a:hover {
    color: var(--color1);
    text-decoration: underline;
}
    </style>
</head>
<body>
    <div class="login-container text-center">
        <h2 class="mb-4">Iniciar Sesión</h2>
        <form action="index.php?c=account&a=login" method="POST">
            <div class="mb-3">
                <input type="text" class="form-control" name="username_or_email" placeholder="Nombre de Usuario o Email" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
            <button type="submit" class="btn btn-login btn-lg w-100">Iniciar Sesión</button>
        </form>
        <p class="mt-4">¿No tienes una cuenta? <a href="index.php?c=account&a=register">Regístrate aquí</a></p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>
    
    <?php
    // Manejador de SweetAlert directamente incrustado
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
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