<?php
// view/main.php
if (session_status() === PHP_SESSION_NONE) session_start();

$isAdmin    = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$isLoggedIn = isset($_SESSION['user_id']);
$username   = $isLoggedIn ? htmlspecialchars($_SESSION['username'] ?? 'Usuario') : '';
$userInitial = $username ? strtoupper($username[0]) : '';
$userId     = $_SESSION['user_id'] ?? null;

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <title>Biblioteca de Formación Docente</title>
  <meta name="theme-color" content="#000e1a">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ── TOP NAVBAR ─────────────────────────────────────────── -->
<nav class="top-nav" id="topNav">
  <a class="nav-brand" href="index.php?c=main">
    <img src="assets/img/logo_fd.png" alt="Logo FD" onerror="this.style.display='none'">
    <span>Biblioteca FD</span>
  </a>

  <!-- Desktop links -->
  <ul class="nav-links">
    <li><a href="#catalogo"><i class="fa-solid fa-book-open"></i> Catálogo</a></li>
    <li>
      <button class="btn-nav-primary btn" onclick="openModal('summaryModal')">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Crear Resumen
      </button>
    </li>
    <li>
      <button class="btn-nav-primary btn" onclick="handleUploadClick()" style="background: linear-gradient(135deg,#07405e,#38bdf8)">
        <i class="fa-solid fa-upload"></i> Cargar Libro
      </button>
    </li>
    <?php if ($isAdmin): ?>
    <li>
      <a href="index.php?c=books&a=managePulls" class="btn-nav-warn btn">
        <i class="fa-solid fa-list-check"></i> Peticiones
      </a>
    </li>
    <?php endif; ?>
    <li>
      <?php if ($isLoggedIn): ?>
      <div style="position:relative">
        <div class="user-pill" onclick="toggleUserDrop()" id="userPill">
          <div class="avatar"><?= $userInitial ?></div>
          <span class="uname"><?= $username ?></span>
          <i class="fa-solid fa-chevron-down" style="font-size:.7rem;color:var(--text-muted)"></i>
        </div>
        <div class="dropdown-menu-fd" id="userDrop">
          <div class="dd-header">
            <small>Sesión iniciada como</small>
            <strong><?= $username ?></strong>
            <?php if ($isAdmin): ?><span class="badge badge-admin"><i class="fa-solid fa-star"></i> Admin</span><?php endif; ?>
          </div>
          <a href="index.php?c=account&a=logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
        </div>
      </div>
      <?php else: ?>
      <a href="index.php?c=account&a=loginForm" class="btn btn-glass" style="padding:.45rem .9rem;font-size:.85rem">
        <i class="fa-solid fa-right-to-bracket"></i> Ingresar
      </a>
      <?php endif; ?>
    </li>
  </ul>

  <!-- Mobile hamburger -->
  <button class="hamburger" id="hamburger" onclick="toggleDrawer()" aria-label="Menú">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- ── SIDE DRAWER (Mobile) ───────────────────────────────── -->
<div class="drawer-overlay" id="drawerOverlay" onclick="toggleDrawer()"></div>
<div class="side-drawer" id="sideDrawer">
  <div class="drawer-header">
    <span style="font-family:'Sora',sans-serif;font-size:1rem;font-weight:600;color:var(--accent)">Biblioteca FD</span>
    <button class="drawer-close" onclick="toggleDrawer()"><i class="fa-solid fa-xmark"></i></button>
  </div>

  <?php if ($isLoggedIn): ?>
  <div class="drawer-user">
    <div style="display:flex;align-items:center;gap:.65rem">
      <div class="avatar" style="width:36px;height:36px;font-size:.9rem"><?= $userInitial ?></div>
      <div>
        <div style="font-size:.9rem;font-weight:600;color:var(--text-primary)"><?= $username ?></div>
        <?php if ($isAdmin): ?><span class="badge badge-admin"><i class="fa-solid fa-star"></i> Admin</span><?php endif; ?>
      </div>
    </div>
  </div>
  <?php else: ?>
  <a class="drawer-link" href="index.php?c=account&a=loginForm"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</a>
  <?php endif; ?>

  <a class="drawer-link active" href="index.php?c=main"><i class="fa-solid fa-house"></i> Inicio</a>
  <a class="drawer-link" href="#catalogo" onclick="toggleDrawer()"><i class="fa-solid fa-book-open"></i> Catálogo</a>
  <button class="drawer-link" onclick="toggleDrawer();openModal('summaryModal')"><i class="fa-solid fa-wand-magic-sparkles"></i> Crear Resumen</button>
  <button class="drawer-link" onclick="toggleDrawer();handleUploadClick()"><i class="fa-solid fa-upload"></i> Cargar Libro</button>
  <?php if ($isAdmin): ?>
  <div class="drawer-divider"></div>
  <a class="drawer-link" href="index.php?c=books&a=managePulls"><i class="fa-solid fa-list-check"></i> Ver Peticiones</a>
  <?php endif; ?>

  <?php if ($isLoggedIn): ?>
  <div class="drawer-divider"></div>
  <a class="drawer-link" href="index.php?c=account&a=logout" style="color:var(--danger)"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
  <?php endif; ?>
</div>

<!-- ── BOTTOM NAV (Mobile) ────────────────────────────────── -->
<nav class="bottom-nav">
  <a href="index.php?c=main" class="bottom-nav-item active">
    <i class="fa-solid fa-house"></i>
    <span>Inicio</span>
  </a>
  <a href="#catalogo" class="bottom-nav-item" onclick="document.getElementById('searchInput').focus()">
    <i class="fa-solid fa-magnifying-glass"></i>
    <span>Buscar</span>
  </a>
  <div class="bottom-nav-fab">
    <button class="fab-btn" onclick="handleUploadClick()" aria-label="Cargar libro">
      <i class="fa-solid fa-plus"></i>
    </button>
    <span class="fab-label">Subir</span>
  </div>
  <?php if ($isLoggedIn): ?>
  <a href="#" class="bottom-nav-item" onclick="openModal('summaryModal');return false">
    <i class="fa-solid fa-wand-magic-sparkles"></i>
    <span>Resumen</span>
  </a>
  <?php else: ?>
  <a href="index.php?c=account&a=loginForm" class="bottom-nav-item">
    <i class="fa-solid fa-right-to-bracket"></i>
    <span>Entrar</span>
  </a>
  <?php endif; ?>
  <?php if ($isAdmin): ?>
  <a href="index.php?c=books&a=managePulls" class="bottom-nav-item">
    <i class="fa-solid fa-list-check"></i>
    <span>Revisión</span>
  </a>
  <?php else: ?>
  <a href="#" class="bottom-nav-item" id="bottomMenuBtn" onclick="toggleDrawer();return false">
    <i class="fa-solid fa-bars"></i>
    <span>Más</span>
  </a>
  <?php endif; ?>
</nav>

<!-- ── MAIN ───────────────────────────────────────────────── -->
<main>

<!-- Hero -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-orb hero-orb-1"></div>
  <div class="hero-orb hero-orb-2"></div>
  <div class="hero-content">
    <div class="hero-badge"><i class="fa-solid fa-graduation-cap"></i> Formación Docente</div>
    <h1 class="hero-title">Biblioteca Digital<br>para Educadores</h1>
    <p class="hero-sub">Accedé a recursos pedagógicos, investigaciones y herramientas para transformar tu práctica docente.</p>
    <div class="hero-actions">
      <a href="#catalogo" class="btn btn-primary btn-lg">
        <i class="fa-solid fa-book-open"></i> Explorar catálogo
      </a>
      <button class="btn btn-glass btn-lg" onclick="openModal('summaryModal')">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Crear resumen IA
      </button>
    </div>
  </div>
  <div class="scroll-indicator">
    <i class="fa-solid fa-chevron-down"></i>
    <span>Explorar</span>
  </div>
</section>

<!-- Search & filters -->
<div class="search-section" id="catalogo">
  <div class="search-bar">
    <div class="search-input-wrap">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="search" class="search-input" id="searchInput" placeholder="Buscar por título, autor o género…" autocomplete="off">
    </div>
    <select class="search-select" id="sortBy">
      <option value="created_at">Más recientes</option>
      <option value="title">Título A–Z</option>
      <option value="author">Autor</option>
      <option value="year">Año</option>
      <option value="likes">Más gustados</option>
    </select>
    <select class="search-select" id="sortOrder" style="min-width:100px">
      <option value="DESC">↓ Desc</option>
      <option value="ASC">↑ Asc</option>
    </select>
  </div>
  <div class="stats-bar">
    <span id="resultCount">Cargando…</span>
    <span id="activeFilters"></span>
  </div>
</div>

<!-- Books grid -->
<section class="books-section">
  <div class="section-header">
    <h2 class="section-title">Catálogo de libros</h2>
    <div class="section-line"></div>
  </div>
  <div class="books-grid" id="booksGrid">
    <!-- Skeletons while loading -->
    <?php for ($i = 0; $i < 8; $i++): ?>
    <div class="skeleton skeleton-card"></div>
    <?php endfor; ?>
  </div>
</section>

</main>

<!-- ── FOOTER ─────────────────────────────────────────────── -->
<footer>
  <p>&copy; 2025 Biblioteca de Formación Docente · Ciudad del Este, Paraguay</p>
  <p style="margin-top:.4rem"><a href="mailto:info@bibliotecadocente.edu.py">info@bibliotecadocente.edu.py</a></p>
</footer>

<!-- ── TOAST CONTAINER ────────────────────────────────────── -->
<div class="toast-container" id="toastContainer"></div>

<!-- ═══════════════════════════════════════════════════════════
     MODALS
═══════════════════════════════════════════════════════════ -->

<!-- Book detail modal -->
<div class="modal-overlay" id="bookModal" onclick="handleOverlayClick(event,'bookModal')">
  <div class="modal modal-lg" role="dialog" aria-labelledby="bookModalTitle">
    <div class="modal-header">
      <h3 class="modal-title-fd" id="bookModalTitle">Detalles del libro</h3>
      <button class="modal-close" onclick="closeModal('bookModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-book-layout">
        <div>
          <img id="modalCover" src="" alt="Portada" class="modal-book-cover">
          <div class="modal-like-row" style="margin-top:.75rem">
            <button class="like-btn-large" id="modalLikeBtn" onclick="toggleLike()">
              <i class="fa-regular fa-heart"></i>
              <span id="modalLikeCount">0</span> Me gusta
            </button>
          </div>
        </div>
        <div class="modal-book-info">
          <h2 id="modalTitle">–</h2>
          <ul class="modal-book-meta">
            <li><strong>Autor</strong> <span id="modalAuthor">–</span></li>
            <li><strong>Año</strong> <span id="modalYear">–</span></li>
            <li><strong>Género</strong> <span id="modalGenre">–</span></li>
            <li id="modalUploaderContainer" style="display:none; color:var(--text-muted); font-size:0.85em;">
              <i class="fa-solid fa-cloud-arrow-up" style="margin-right:.25rem"></i><strong>Subido por:</strong> <span id="modalUploader">–</span>
            </li>
          </ul>
          <p class="modal-book-desc" id="modalDesc">–</p>
        </div>
      </div>
    </div>
    <div class="modal-footer-fd">
      <a id="modalResumeBtn" href="#" target="_blank" class="btn btn-primary" style="display:none">
        <i class="fa-solid fa-eye"></i> Ver resumen
      </a>
      <a id="modalPdfBtn" href="#" target="_blank" class="btn btn-glass" style="display:none">
        <i class="fa-solid fa-file-pdf"></i> Descargar PDF
      </a>
      <?php if ($isAdmin): ?>
      <button id="modalDeleteBtn" class="btn btn-danger" onclick="deleteCurrentBook()" style="display:none">
        <i class="fa-solid fa-trash"></i> Eliminar
      </button>
      <?php endif; ?>
      <button class="btn btn-glass" onclick="closeModal('bookModal')">Cerrar</button>
    </div>
  </div>
</div>

<!-- Upload book modal -->
<div class="modal-overlay" id="uploadModal" onclick="handleOverlayClick(event,'uploadModal')">
  <div class="modal" role="dialog" aria-labelledby="uploadModalTitle">
    <div class="modal-header">
      <h3 class="modal-title-fd" id="uploadModalTitle"><i class="fa-solid fa-upload" style="color:var(--accent)"></i> Cargar nuevo libro</h3>
      <button class="modal-close" onclick="closeModal('uploadModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <form id="uploadForm" action="index.php?c=books&a=upload" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label class="form-label">Título <span style="color:var(--danger)">*</span></label>
          <input type="text" class="form-control" name="title" required placeholder="Título del libro">
        </div>
        <div class="form-group">
          <label class="form-label">Autor(es) <span style="color:var(--danger)">*</span></label>
          <input type="text" class="form-control" name="author" required placeholder="Nombre del autor">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Año</label>
            <input type="number" class="form-control" name="year" min="1800" max="<?= date('Y') + 5 ?>" value="<?= date('Y') ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Género / Categoría</label>
            <input type="text" class="form-control" name="genre" placeholder="Educación, Pedagogía…">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Descripción</label>
          <textarea class="form-control" name="description" rows="3" placeholder="Breve descripción del contenido…"></textarea>
        </div>
        <div class="form-group">
          <label class="form-label">Imagen de portada</label>
          <input type="file" class="form-control-file" name="cover_image" accept="image/jpeg,image/png,image/gif">
          <p class="form-hint">JPG, PNG o GIF · máx. 5 MB</p>
        </div>
        <div class="form-group">
          <label class="form-label">Archivo PDF <span style="color:var(--danger)">*</span></label>
          <input type="file" class="form-control-file" name="pdf_file" accept="application/pdf" required>
          <p class="form-hint">Máx. 50 MB</p>
        </div>
        <div class="form-group">
          <label class="form-label">URL del resumen (opcional)</label>
          <input type="url" class="form-control" name="summary_url" placeholder="https://gamma.app/…">
        </div>
      </form>
    </div>
    <div class="modal-footer-fd">
      <button class="btn btn-glass" onclick="closeModal('uploadModal')">Cancelar</button>
      <button class="btn btn-primary" onclick="document.getElementById('uploadForm').submit()">
        <i class="fa-solid fa-floppy-disk"></i> Enviar solicitud
      </button>
    </div>
  </div>
</div>

<!-- Create Summary guide modal -->
<div class="modal-overlay" id="summaryModal" onclick="handleOverlayClick(event,'summaryModal')">
  <div class="modal modal-lg" role="dialog" aria-labelledby="summaryModalTitle">
    <div class="modal-header">
      <h3 class="modal-title-fd" id="summaryModalTitle"><i class="fa-solid fa-lightbulb" style="color:var(--gold)"></i> Crear un resumen con IA</h3>
      <button class="modal-close" onclick="closeModal('summaryModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="step-cards">
        <div class="step-card">
          <div class="step-icon"><i class="fa-solid fa-robot"></i></div>
          <h6>Paso 1 — Prompt</h6>
          <div class="prompt-box" id="summaryPromptText">
            "Actúa como experto en análisis de contenido. Resume este PDF destacando ideas clave, argumentos principales y conclusiones, de forma concisa y objetiva. Tono formal y académico. Entre 500 y 700 palabras."
          </div>
          <button class="btn btn-glass btn-sm w-100" onclick="copyPrompt(this)">
            <i class="fa-solid fa-copy"></i> Copiar prompt
          </button>
        </div>
        <div class="step-card">
          <div class="step-icon"><i class="fa-solid fa-file-pdf"></i></div>
          <h6>Paso 2 — ChatGPT + PDF</h6>
          <p>Abre ChatGPT (o tu IA preferida), pega el prompt y sube el PDF del libro que querés resumir.</p>
          <p style="margin-top:.5rem;font-size:.78rem;color:var(--text-muted)">Esperá que la IA genere el resumen en texto.</p>
        </div>
        <div class="step-card">
          <div class="step-icon"><i class="fa-solid fa-cube"></i></div>
          <h6>Paso 3 — Gamma.app</h6>
          <p>Copiá el resumen y pegálo en Gamma para crear una presentación interactiva y visualmente atractiva.</p>
          <a href="https://gamma.app/create" target="_blank" class="btn btn-primary btn-sm w-100" style="margin-top:.75rem;justify-content:center">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Ir a Gamma.app
          </a>
        </div>
      </div>
    </div>
    <div class="modal-footer-fd">
      <button class="btn btn-glass" onclick="closeModal('summaryModal')">Entendido</button>
    </div>
  </div>
</div>

<!-- Login required modal -->
<div class="modal-overlay" id="loginRequiredModal" onclick="handleOverlayClick(event,'loginRequiredModal')">
  <div class="modal modal-sm" role="dialog">
    <div class="modal-header">
      <h3 class="modal-title-fd"><i class="fa-solid fa-lock" style="color:var(--gold)"></i> Acceso requerido</h3>
      <button class="modal-close" onclick="closeModal('loginRequiredModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body" style="text-align:center;padding:2rem">
      <i class="fa-solid fa-user-lock" style="font-size:2.5rem;color:var(--text-muted);margin-bottom:1rem;display:block"></i>
      <p style="color:var(--text-secondary);margin-bottom:1.5rem">Necesitás iniciar sesión para cargar un libro.</p>
      <a href="index.php?c=account&a=loginForm" class="btn btn-primary w-100" style="justify-content:center">
        <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
      </a>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     SCRIPTS
═══════════════════════════════════════════════════════════ -->
<script>
// ── Data from PHP ────────────────────────────────────────────
const ALL_BOOKS = <?= json_encode(array_map(function($b) {
  return [
    'id'          => (int)$b['id'],
    'title'       => $b['title'] ?? '',
    'author'      => $b['author'] ?? '',
    'year'        => (int)($b['year'] ?? 0),
    'genre'       => $b['genre'] ?? '',
    'description' => $b['description'] ?? '',
    'image_path'  => $b['image_path'] ?? '',
    'pdf_path'    => $b['pdf_path'] ?? '',
    'url_resumen' => $b['url_resumen'] ?? '',
    'likes'       => (int)($b['likes'] ?? 0),
    'user_liked'  => (bool)($b['user_liked'] ?? false),
    'created_at'  => $b['created_at'] ?? '',
    'uploader_email'=> $b['uploader_email'] ?? '',
  ];
}, $libros ?? [])) ?>;

const IS_LOGGED_IN = <?= $isLoggedIn ? 'true' : 'false' ?>;
const IS_ADMIN     = <?= $isAdmin ? 'true' : 'false' ?>;


// ── State ────────────────────────────────────────────────────
let activeBookId = null;
let displayedBooks = [];

// ── MODAL SYSTEM ─────────────────────────────────────────────
function openModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  const el = document.getElementById(id);
  if (!el) return;
  el.classList.remove('open');
  document.body.style.overflow = '';
}
function handleOverlayClick(e, id) {
  if (e.target === e.currentTarget) closeModal(id);
}
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(m => {
      m.classList.remove('open');
    });
    document.body.style.overflow = '';
  }
});

// ── NAV HELPERS ──────────────────────────────────────────────
function toggleDrawer() {
  const drawer  = document.getElementById('sideDrawer');
  const overlay = document.getElementById('drawerOverlay');
  const burger  = document.getElementById('hamburger');
  drawer.classList.toggle('open');
  overlay.classList.toggle('open');
  burger.classList.toggle('active');
}
function toggleUserDrop() {
  document.getElementById('userDrop').classList.toggle('open');
}
document.addEventListener('click', e => {
  const pill = document.getElementById('userPill');
  const drop = document.getElementById('userDrop');
  if (pill && drop && !pill.contains(e.target)) drop.classList.remove('open');
});

// Navbar scroll effect
const topNav = document.getElementById('topNav');
window.addEventListener('scroll', () => {
  topNav.classList.toggle('scrolled', window.scrollY > 40);
}, { passive: true });

// ── TOAST ────────────────────────────────────────────────────
function showToast(message, type = 'success') {
  const icons = { success: 'fa-circle-check', error: 'fa-circle-exclamation', warning: 'fa-triangle-exclamation' };
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.success}"></i><span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.classList.add('toast-out');
    toast.addEventListener('animationend', () => toast.remove());
  }, 3500);
}

// ── PHP SESSION ALERTS ────────────────────────────────────────
<?php if (isset($_SESSION['sweet_alert'])): 
  $alert = $_SESSION['sweet_alert'];
  unset($_SESSION['sweet_alert']);
?>
document.addEventListener('DOMContentLoaded', () => {
  showToast(<?= json_encode($alert['title']) ?>, <?= json_encode($alert['type']) ?>);
});
<?php endif; ?>

// ── UPLOAD HANDLER ────────────────────────────────────────────
function handleUploadClick() {
  if (IS_LOGGED_IN) {
    openModal('uploadModal');
  } else {
    openModal('loginRequiredModal');
  }
}

// ── RENDER BOOKS ──────────────────────────────────────────────
function renderBooks(books) {
  const grid = document.getElementById('booksGrid');
  grid.innerHTML = '';

  if (!books.length) {
    grid.innerHTML = `
      <div class="empty-state">
        <i class="fa-solid fa-box-open"></i>
        <h3>Sin resultados</h3>
        <p>Probá con otros términos de búsqueda o filtros.</p>
      </div>`;
    return;
  }

  books.forEach((book, i) => {
    const likedClass = book.user_liked ? 'liked' : '';
    const heartIcon  = book.user_liked ? 'fa-solid' : 'fa-regular';
    const card = document.createElement('div');
    card.className = 'book-card';
    card.style.animationDelay = `${Math.min(i * 0.04, 0.4)}s`;
    card.innerHTML = `
      <div class="book-cover-wrap">
        <img class="book-cover"
          src="${escHtml(book.image_path)}"
          alt="${escHtml(book.title)}"
          loading="lazy"
          onerror="this.src='assets/img/book_placeholder.png'">
        <button class="card-like-btn ${likedClass}"
          onclick="toggleLikeCard(event,${book.id},this)"
          aria-label="Me gusta"
          ${IS_LOGGED_IN ? '' : 'disabled'}>
          <i class="${heartIcon} fa-heart"></i>
        </button>
      </div>
      <div class="book-info">
        <h3 class="book-title-card">${escHtml(book.title)}</h3>
        <p class="book-author-card">${escHtml(book.author)}</p>
        <div class="book-meta-row">
          <span class="book-genre-tag">${escHtml(book.genre || 'General')}</span>
          <span class="book-likes-count">
            <i class="fa-solid fa-heart"></i> ${book.likes}
          </span>
        </div>
      </div>`;
    card.addEventListener('click', e => {
      if (e.target.closest('.card-like-btn')) return;
      openBookDetail(book.id);
    });
    grid.appendChild(card);
  });
}

function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = String(str ?? '');
  return d.innerHTML;
}

// ── FILTER & SORT ─────────────────────────────────────────────
function applyFilters() {
  const q     = document.getElementById('searchInput').value.toLowerCase().trim();
  const by    = document.getElementById('sortBy').value;
  const order = document.getElementById('sortOrder').value;

  let filtered = ALL_BOOKS.filter(b => {
    if (!q) return true;
    return (b.title + b.author + b.genre).toLowerCase().includes(q);
  });

  filtered.sort((a, b) => {
    let va, vb;
    if (by === 'created_at') {
      va = new Date(a.created_at || 0).getTime();
      vb = new Date(b.created_at || 0).getTime();
    } else if (by === 'year' || by === 'likes') {
      va = a[by] || 0; vb = b[by] || 0;
    } else {
      va = (a[by] || '').toLowerCase();
      vb = (b[by] || '').toLowerCase();
    }
    if (va < vb) return order === 'ASC' ? -1 : 1;
    if (va > vb) return order === 'ASC' ? 1 : -1;
    return 0;
  });

  displayedBooks = filtered;
  renderBooks(filtered);

  // Update count
  document.getElementById('resultCount').innerHTML =
    `<strong>${filtered.length}</strong> de <strong>${ALL_BOOKS.length}</strong> libros`;
  document.getElementById('activeFilters').textContent =
    q ? `· Búsqueda: "${q}"` : '';
}

// Debounce search
let searchTimer;
document.getElementById('searchInput').addEventListener('input', () => {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(applyFilters, 250);
});
document.getElementById('sortBy').addEventListener('change', applyFilters);
document.getElementById('sortOrder').addEventListener('change', applyFilters);

// ── BOOK DETAIL ───────────────────────────────────────────────
function openBookDetail(bookId) {
  const book = ALL_BOOKS.find(b => b.id === bookId);
  if (!book) return;
  activeBookId = bookId;

  document.getElementById('modalCover').src  = book.image_path || 'assets/img/book_placeholder.png';
  document.getElementById('modalTitle').textContent  = book.title;
  document.getElementById('modalAuthor').textContent = book.author;
  document.getElementById('modalYear').textContent   = book.year || '–';
  document.getElementById('modalGenre').textContent  = book.genre || '–';
  document.getElementById('modalDesc').textContent   = book.description || 'Sin descripción disponible.';
  document.getElementById('modalLikeCount').textContent = book.likes;

  const uploaderCont = document.getElementById('modalUploaderContainer');
  if (book.uploader_email) {
    document.getElementById('modalUploader').textContent = book.uploader_email;
    uploaderCont.style.display = '';
  } else {
    uploaderCont.style.display = 'none';
  }

  const likeBtn = document.getElementById('modalLikeBtn');
  likeBtn.className = 'like-btn-large' + (book.user_liked ? ' liked' : '');
  likeBtn.querySelector('i').className = (book.user_liked ? 'fa-solid' : 'fa-regular') + ' fa-heart';

  const resumeBtn = document.getElementById('modalResumeBtn');
  const pdfBtn    = document.getElementById('modalPdfBtn');
  const deleteBtn = document.getElementById('modalDeleteBtn');

  if (book.url_resumen) { resumeBtn.href = book.url_resumen; resumeBtn.style.display = ''; }
  else resumeBtn.style.display = 'none';

  if (book.pdf_path) { pdfBtn.href = book.pdf_path; pdfBtn.style.display = ''; }
  else pdfBtn.style.display = 'none';

  if (deleteBtn) deleteBtn.style.display = IS_ADMIN ? '' : 'none';

  openModal('bookModal');
}

function deleteCurrentBook() {
  if (!activeBookId || !IS_ADMIN) return;
  if (!confirm('¿Eliminar este libro permanentemente? Esta acción no se puede deshacer.')) return;
  window.location.href = `index.php?c=books&a=delete&id=${activeBookId}`;
}

// ── LIKES ─────────────────────────────────────────────────────
async function sendLike(bookId) {
  try {
    const res = await fetch(`index.php?c=books&a=toggleLike`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `book_id=${bookId}`
    });
    const data = await res.json();
    return data;
  } catch {
    return null;
  }
}

async function toggleLike() {
  if (!IS_LOGGED_IN) { showToast('Iniciá sesión para dar me gusta', 'warning'); return; }
  const book = ALL_BOOKS.find(b => b.id === activeBookId);
  if (!book) return;

  const result = await sendLike(activeBookId);
  if (!result) { showToast('Error al procesar el me gusta', 'error'); return; }

  book.user_liked = result.liked;
  book.likes      = result.count;

  const likeBtn   = document.getElementById('modalLikeBtn');
  const likeCount = document.getElementById('modalLikeCount');
  likeBtn.className = 'like-btn-large' + (result.liked ? ' liked' : '');
  likeBtn.querySelector('i').className = (result.liked ? 'fa-solid' : 'fa-regular') + ' fa-heart';
  likeCount.textContent = result.count;

  // Update card
  const cardBtn = document.querySelector(`.book-card [onclick*="toggleLikeCard"][onclick*="${activeBookId}"]`);
  if (cardBtn) {
    cardBtn.className = 'card-like-btn' + (result.liked ? ' liked' : '');
    cardBtn.querySelector('i').className = (result.liked ? 'fa-solid' : 'fa-regular') + ' fa-heart';
    const countEl = cardBtn.closest('.book-card').querySelector('.book-likes-count');
    if (countEl) countEl.innerHTML = `<i class="fa-solid fa-heart"></i> ${result.count}`;
  }
}

async function toggleLikeCard(e, bookId, btn) {
  e.stopPropagation();
  if (!IS_LOGGED_IN) { showToast('Iniciá sesión para dar me gusta', 'warning'); return; }

  const book = ALL_BOOKS.find(b => b.id === bookId);
  if (!book) return;

  const result = await sendLike(bookId);
  if (!result) { showToast('Error al procesar el me gusta', 'error'); return; }

  book.user_liked = result.liked;
  book.likes      = result.count;

  btn.className = 'card-like-btn' + (result.liked ? ' liked' : '');
  btn.querySelector('i').className = (result.liked ? 'fa-solid' : 'fa-regular') + ' fa-heart';
  const countEl = btn.closest('.book-card').querySelector('.book-likes-count');
  if (countEl) countEl.innerHTML = `<i class="fa-solid fa-heart"></i> ${result.count}`;

  // Animate
  btn.animate([{transform:'scale(1.4)'},{transform:'scale(1)'}], {duration:300,easing:'ease-out'});
}

// ── PROMPT COPY ───────────────────────────────────────────────
function copyPrompt(btn) {
  const text = document.getElementById('summaryPromptText').textContent.trim();
  navigator.clipboard.writeText(text).then(() => {
    btn.innerHTML = '<i class="fa-solid fa-check"></i> ¡Copiado!';
    btn.style.color = 'var(--success)';
    setTimeout(() => {
      btn.innerHTML = '<i class="fa-solid fa-copy"></i> Copiar prompt';
      btn.style.color = '';
    }, 2000);
  });
}

// ── SMOOTH SCROLL ─────────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (!target) return;
    e.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

// ── INIT ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  applyFilters();
});
</script>
</body>
</html>