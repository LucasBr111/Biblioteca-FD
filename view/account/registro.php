<?php
// view/account/registro.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php?c=main'); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Cuenta — Biblioteca FD</title>
  <meta name="theme-color" content="#000e1a">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="hero-orb hero-orb-1" style="opacity:.15;width:500px;height:500px;top:-150px;left:-150px"></div>
<div class="hero-orb hero-orb-2" style="opacity:.08;width:350px;height:350px;bottom:-80px;right:-80px"></div>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <img src="assets/img/logo_fd.png" alt="Logo" onerror="this.style.display='none'">
      <h1>Biblioteca FD</h1>
      <p>Creá tu cuenta para unirte a la biblioteca</p>
    </div>

    <form action="index.php?c=account&a=register" method="POST" id="registerForm">
      
      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-user" style="margin-right:.35rem;color:var(--accent)"></i>Nombre de usuario</label>
        <input type="text" class="form-control" name="username" id="username"
          placeholder="Elegí un nombre de usuario" required autofocus>
      </div>

      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-envelope" style="margin-right:.35rem;color:var(--accent)"></i>Correo electrónico</label>
        <input type="email" class="form-control" name="email" id="email"
          placeholder="Ingresá tu correo" required>
      </div>

      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-lock" style="margin-right:.35rem;color:var(--accent)"></i>Contraseña</label>
        <div style="position:relative">
          <input type="password" class="form-control" name="password" id="passInput1"
            placeholder="Creá una contraseña" required style="padding-right:2.5rem">
          <button type="button"
            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:.9rem"
            onclick="togglePass('passInput1', this)" tabindex="-1">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-check-circle" style="margin-right:.35rem;color:var(--accent)"></i>Confirmar contraseña</label>
        <div style="position:relative">
          <input type="password" class="form-control" name="confirm_password" id="passInput2"
            placeholder="Repetí tu contraseña" required style="padding-right:2.5rem">
          <button type="button"
            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:.9rem"
            onclick="togglePass('passInput2', this)" tabindex="-1">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100" style="justify-content:center;margin-top:.5rem;padding:.75rem">
        <i class="fa-solid fa-user-plus"></i> Registrarme
      </button>
    </form>

    <div style="margin-top:1.25rem;text-align:center">
      <a href="index.php?c=main" style="font-size:.85rem;color:var(--text-muted)">
        <i class="fa-solid fa-arrow-left"></i> Volver al inicio
      </a>
    </div>

    <div style="margin-top:1rem;padding:.75rem;border-radius:var(--radius-sm);background:rgba(56,189,248,0.07);border:1px solid rgba(56,189,248,0.15);font-size:.85rem;color:var(--text-muted);text-align:center">
      ¿Ya tenés una cuenta? <a href="index.php?c=account&a=loginForm" style="color:var(--accent);font-weight:600">Ingresar</a>
    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
function togglePass(inputId, btn) {
  const input = document.getElementById(inputId);
  const showing = input.type === 'text';
  input.type = showing ? 'password' : 'text';
  btn.querySelector('i').className = showing ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
}

function showToast(message, type = 'success') {
  const icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', warning: 'fa-triangle-exclamation' };
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<i class="fa-solid ${icons[type]}"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.classList.add('toast-out');
    toast.addEventListener('animationend', () => toast.remove());
  }, 3500);
}

// Consumo de las alertas desde el backend (PHP a JS)
<?php if (isset($_SESSION['sweet_alert'])):
  $alert = $_SESSION['sweet_alert'];
  unset($_SESSION['sweet_alert']); ?>
document.addEventListener('DOMContentLoaded', () => {
  showToast(<?= json_encode($alert['title'] . (isset($alert['text']) && !empty($alert['text']) ? " - " . $alert['text'] : "")) ?>, <?= json_encode($alert['type']) ?>);
});
<?php endif; ?>

// --- VALIDACIONES FRONTEND CON JAVASCRIPT ---
document.getElementById('registerForm').addEventListener('submit', function(e) {
    // 1. Obtener los valores y limpiarlos de espacios en blanco
    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('passInput1').value;
    const confirm = document.getElementById('passInput2').value;

    // Validación 1: Campos vacíos (Aunque HTML 'required' ayuda, esto lo refuerza)
    if (!username || !email || !password || !confirm) {
        e.preventDefault();
        showToast('Debes completar todos los campos del formulario.', 'warning');
        return;
    }

    // Validación 2: Formato de email con Expresión Regular
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        showToast('El formato del correo electrónico no es válido.', 'warning');
        return;
    }

    // Validación 3: Longitud de usuario
    if (username.length < 3 || username.length > 20) {
        e.preventDefault();
        showToast('El usuario debe tener entre 3 y 20 caracteres.', 'warning');
        return;
    }

    // Validación 4: Longitud de contraseña
    if (password.length < 6) {
        e.preventDefault();
        showToast('La contraseña es muy corta. Usa al menos 6 caracteres.', 'warning');
        return;
    }

    // Validación 5: Coincidencia de contraseñas
    if (password !== confirm) {
        e.preventDefault();
        showToast('Las contraseñas no coinciden. Verifícalas e inténtalo de nuevo.', 'error');
        return;
    }

    // Si todo está correcto, el evento 'submit' continuará naturalmente y enviará el POST a PHP.
});
</script>
</body>
</html>