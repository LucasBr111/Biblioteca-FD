<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <!--<style>
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
    </style>-->
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

.register-container {

    background-color: rgba(255, 255, 255, 0.98); 
    padding: 3.5rem; 
    border-radius: 1.5rem; 
    box-shadow: 0 15px 40px var(--shadow-dark);
    max-width: 550px; 
    width: 100%;
    text-align: center; 
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.register-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px var(--shadow-dark);
}

.register-container h2 {
    color: var(--color1); 
    margin-bottom: 2.2rem; 
    font-weight: 700;
    font-size: 2.4rem; 
    letter-spacing: 0.8px; 
    position: relative;
    padding-bottom: 12px; 
}

.register-container h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px; 
    height: 4px; 
    background-color: var(--color2); 
    border-radius: 2px;
}

.form-control { 
    border: 1px solid var(--color4); 
    padding: 0.9rem 1.3rem;
    border-radius: 0.8rem; 
    font-size: 1.05rem;
    color: var(--text-dark);
    margin-bottom: 1rem; 
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    border-color: var(--color1); 
    box-shadow: 0 0 0 0.25rem rgba(12, 87, 124, 0.25); 
}

/* --- Botón de Registro --- */
.btn-register {
    background-color: var(--color1); 
    border-color: var(--color1);
    color: var(--text-light);
    padding: 1.1rem 3rem; 
    font-size: 1.2rem; 
    font-weight: 600;
    border-radius: 0.8rem; 
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2); 
    transition: all 0.3s ease; 
    width: 100%; 
    margin-top: 2rem; 
}

.btn-register:hover {
    background-color: var(--color2); 
    border-color: var(--color2);
    transform: translateY(-3px); 
    box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
}

.btn-register:active {
    background-color: var(--color3); 
    border-color: var(--color3);
    transform: translateY(0); 
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}


.register-links {
    margin-top: 1.5rem;
    font-size: 0.95rem;
}

.register-links a {
    color: var(--color3); 
    text-decoration: none;
    transition: color 0.3s ease;
}

.register-links a:hover {
    color: var(--color1); 
    text-decoration: underline;
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
        <p class="mt-4">¿Ya tienes una cuenta? <a href="index.php?c=account&a=login">Inicia Sesión aquí</a></p>
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