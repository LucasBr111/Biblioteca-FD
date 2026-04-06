<?php
// view/account/login.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php?c=main'); exit(); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingresar — Biblioteca FD</title>
  <meta name="theme-color" content="#000e1a">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Orbs de fondo -->
<div class="hero-orb hero-orb-1" style="opacity:.15;width:500px;height:500px;top:-150px;left:-150px"></div>
<div class="hero-orb hero-orb-2" style="opacity:.08;width:350px;height:350px;bottom:-80px;right:-80px"></div>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <img src="assets/img/logo_fd.png" alt="Logo" onerror="this.style.display='none'">
      <h1>Biblioteca FD</h1>
      <p>Ingresá para acceder a todas las funciones</p>
    </div>

    <form action="index.php?c=account&a=login" method="POST" id="loginForm">
      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-user" style="margin-right:.35rem;color:var(--accent)"></i>Usuario o email</label>
        <input type="text" class="form-control" name="username_or_email"
          placeholder="Ingresá tu usuario o email"
          autocomplete="username" required autofocus>
      </div>
      <div class="form-group">
        <label class="form-label"><i class="fa-solid fa-lock" style="margin-right:.35rem;color:var(--accent)"></i>Contraseña</label>
        <div style="position:relative">
          <input type="password" class="form-control" name="password" id="passInput"
            placeholder="Tu contraseña"
            autocomplete="current-password" required style="padding-right:2.5rem">
          <button type="button"
            style="position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:.9rem"
            onclick="togglePass(this)" tabindex="-1">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100" style="justify-content:center;margin-top:.5rem;padding:.75rem">
        <i class="fa-solid fa-right-to-bracket"></i> Ingresar
      </button>
    </form>

    <div style="margin-top:1.25rem;text-align:center">
      <a href="index.php?c=main" style="font-size:.85rem;color:var(--text-muted)">
        <i class="fa-solid fa-arrow-left"></i> Volver al inicio
      </a>
    </div>

    <div style="margin-top:1rem;padding:.75rem;border-radius:var(--radius-sm);background:rgba(56,189,248,0.07);border:1px solid rgba(56,189,248,0.15);font-size:.85rem;color:var(--text-muted);text-align:center">
      ¿No tenés una cuenta? <a href="index.php?c=account&a=registerForm" style="color:var(--accent);font-weight:600">Registrarme</a>
    </div>
  </div>
</div>

<!-- Toast container -->
<div class="toast-container" id="toastContainer"></div>

<script>
function togglePass(btn) {
  const input = document.getElementById('passInput');
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

<?php if (isset($_SESSION['sweet_alert'])):
  $alert = $_SESSION['sweet_alert'];
  unset($_SESSION['sweet_alert']); ?>
document.addEventListener('DOMContentLoaded', () => {
  showToast(<?= json_encode($alert['title']) ?>, <?= json_encode($alert['type']) ?>);
});
<?php endif; ?>
</script>
</body>
</html>