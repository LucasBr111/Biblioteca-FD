<?php
// view/books/manage_pulls.php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: index.php?c=main'); exit();
}
$pulls = $pulls ?? [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Solicitudes — Biblioteca FD</title>
  <meta name="theme-color" content="#000e1a">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Top nav minimal -->
<nav class="top-nav scrolled">
  <a class="nav-brand" href="index.php?c=main">
    <img src="assets/img/logo_fd.png" alt="Logo" onerror="this.style.display='none'">
    <span>Biblioteca FD</span>
  </a>
  <ul class="nav-links">
    <a href="index.php?c=main" class="btn btn-glass btn-sm"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    <span class="badge badge-admin" style="margin-left:.5rem"><i class="fa-solid fa-star"></i> Admin</span>
  </ul>
</nav>

<!-- Toast -->
<div class="toast-container" id="toastContainer"></div>

<main style="padding-top:calc(var(--nav-h) + 1.5rem)">
  <div class="admin-section">
    <div class="admin-header">
      <i class="fa-solid fa-list-check" style="font-size:1.5rem;color:var(--accent)"></i>
      <div>
        <h1>Gestión de solicitudes</h1>
        <p style="font-size:.85rem;color:var(--text-muted);margin-top:.15rem">
          Revisá y aprobá o rechazá los libros enviados por usuarios.
        </p>
      </div>
      <div style="margin-left:auto">
        <span class="badge" style="background:rgba(56,189,248,.1);color:var(--accent);border:1px solid rgba(56,189,248,.25);padding:.4rem .9rem;font-size:.85rem">
          <?= count($pulls) ?> pendiente<?= count($pulls) !== 1 ? 's' : '' ?>
        </span>
      </div>
    </div>

    <?php if (empty($pulls)): ?>
    <div class="empty-state" style="background:var(--glass-bg);border:1px solid var(--glass-border);border-radius:var(--radius-lg);padding:4rem">
      <i class="fa-solid fa-inbox"></i>
      <h3>Sin solicitudes pendientes</h3>
      <p>Cuando alguien envíe un libro para revisión, aparecerá aquí.</p>
    </div>
    <?php else: ?>
    <div class="pulls-grid">
      <?php foreach ($pulls as $book): ?>
      <div class="pull-card" id="pull-<?= $book['id'] ?>">
        <div class="pull-card-inner">
          <!-- Cover -->
          <div>
            <?php if (!empty($book['image_path']) && file_exists($book['image_path'])): ?>
            <img src="<?= htmlspecialchars($book['image_path']) ?>" alt="Portada" class="pull-cover">
            <?php else: ?>
            <div class="pull-cover" style="background:var(--glass-bg-md);display:flex;align-items:center;justify-content:center;color:var(--text-muted)">
              <i class="fa-solid fa-image fa-lg"></i>
            </div>
            <?php endif; ?>
          </div>

          <!-- Info -->
          <div class="pull-info">
            <h3><?= htmlspecialchars($book['title']) ?></h3>
            <p><i class="fa-solid fa-user-pen" style="color:var(--accent);margin-right:.3rem"></i><?= htmlspecialchars($book['author']) ?></p>
            <p style="margin-top:.2rem"><i class="fa-solid fa-calendar" style="color:var(--text-muted);margin-right:.3rem"></i><?= htmlspecialchars($book['year'] ?? '–') ?></p>
            <?php if (!empty($book['uploader_email'])): ?>
            <p style="margin-top:.2rem"><i class="fa-solid fa-cloud-arrow-up" style="color:var(--text-muted);margin-right:.3rem"></i>Enviado por: <?= htmlspecialchars($book['uploader_email']) ?></p>
            <?php endif; ?>
            <?php if (!empty($book['genre'])): ?>
            <span class="book-genre-tag" style="margin-top:.4rem;display:inline-block"><?= htmlspecialchars($book['genre']) ?></span>
            <?php endif; ?>
          </div>

          <!-- Actions -->
          <div class="pull-actions">
            <button class="btn btn-success btn-sm"
              onclick="confirmAction(<?= $book['id'] ?>, 'accept')"
              title="Publicar">
              <i class="fa-solid fa-check"></i> Publicar
            </button>
            <button class="btn btn-danger btn-sm"
              onclick="confirmAction(<?= $book['id'] ?>, 'deny')"
              title="Rechazar">
              <i class="fa-solid fa-xmark"></i> Rechazar
            </button>
            <?php if (!empty($book['pdf_path'])): ?>
            <a href="<?= htmlspecialchars($book['pdf_path']) ?>" target="_blank" class="btn btn-glass btn-sm">
              <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <?php endif; ?>
            <?php if (!empty($book['url_resumen'])): ?>
            <a href="<?= htmlspecialchars($book['url_resumen']) ?>" target="_blank" class="btn btn-glass btn-sm">
              <i class="fa-solid fa-eye"></i> Resumen
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</main>

<!-- Confirm modal -->
<div class="modal-overlay" id="confirmModal">
  <div class="modal modal-sm" role="dialog">
    <div class="modal-header">
      <h3 class="modal-title-fd" id="confirmTitle">Confirmar acción</h3>
      <button class="modal-close" onclick="closeConfirm()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="text-align:center;padding:1.5rem">
      <i id="confirmIcon" class="fa-solid" style="font-size:2rem;margin-bottom:1rem;display:block"></i>
      <p id="confirmMsg" style="color:var(--text-secondary)"></p>
    </div>
    <div class="modal-footer-fd">
      <button class="btn btn-glass" onclick="closeConfirm()">Cancelar</button>
      <a id="confirmBtn" href="#" class="btn">Confirmar</a>
    </div>
  </div>
</div>

<script>
function showToast(message, type = 'success') {
  const icons = { success:'fa-circle-check', error:'fa-circle-exclamation', warning:'fa-triangle-exclamation' };
  const c = document.getElementById('toastContainer');
  const t = document.createElement('div');
  t.className = `toast toast-${type}`;
  t.innerHTML = `<i class="fa-solid ${icons[type]}"></i><span>${message}</span>`;
  c.appendChild(t);
  setTimeout(() => { t.classList.add('toast-out'); t.addEventListener('animationend',()=>t.remove()); }, 3500);
}

<?php if (isset($_SESSION['sweet_alert'])):
  $a = $_SESSION['sweet_alert']; unset($_SESSION['sweet_alert']); ?>
document.addEventListener('DOMContentLoaded', () => showToast(<?= json_encode($a['title']) ?>, <?= json_encode($a['type']) ?>));
<?php endif; ?>


function confirmAction(id, action) {
  const isAccept = action === 'accept';
  document.getElementById('confirmTitle').textContent = isAccept ? 'Publicar libro' : 'Rechazar solicitud';
  document.getElementById('confirmIcon').className =
    'fa-solid ' + (isAccept ? 'fa-circle-check' : 'fa-circle-xmark');
  document.getElementById('confirmIcon').style.color = isAccept ? 'var(--success)' : 'var(--danger)';
  document.getElementById('confirmMsg').textContent = isAccept
    ? '¿Publicar este libro en el catálogo? Los archivos serán movidos a su ubicación final.'
    : '¿Rechazar esta solicitud? El libro y sus archivos temporales serán eliminados permanentemente.';
  const btn = document.getElementById('confirmBtn');
  btn.href = `index.php?c=books&a=${isAccept ? 'acceptPull' : 'denyPull'}&id=${id}`;
  btn.className = 'btn ' + (isAccept ? 'btn-success' : 'btn-danger');
  btn.textContent = isAccept ? 'Sí, publicar' : 'Sí, rechazar';
  document.getElementById('confirmModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeConfirm() {
  document.getElementById('confirmModal').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('confirmModal').addEventListener('click', e => {
  if (e.target === e.currentTarget) closeConfirm();
});
</script>
</body>
</html>