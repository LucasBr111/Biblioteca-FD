<?php
// view/books/favorites.php
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
  <title>Mis Favoritos | Biblioteca FD</title>
  <meta name="theme-color" content="#000e1a">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .favorites-header {
      padding: 6rem 5% 2rem; /* Give space for fixed nav */
      text-align: center;
      background: linear-gradient(to bottom, #001f3f, var(--bg-main));
    }
    .favorites-title {
      font-family: 'Sora', sans-serif;
      font-size: 2.5rem;
      color: #fff;
    }
  </style>
</head>
<body>

<!-- ── TOP NAVBAR ─────────────────────────────────────────── -->
<nav class="top-nav scrolled" id="topNav" style="background: rgba(0, 5, 10, 0.95)">
  <a class="nav-brand" href="index.php?c=main">
    <img src="assets/img/logo_fd.png" alt="Logo FD" onerror="this.style.display='none'">
    <span>Biblioteca FD</span>
  </a>
  <ul class="nav-links">
    <li><a href="index.php?c=main"><i class="fa-solid fa-house"></i> Inicio</a></li>
    <?php if ($isAdmin): ?>
    <li>
      <a href="index.php?c=account&a=adminDashboard" class="btn-nav-warn btn">
        <i class="fa-solid fa-chart-line"></i> Dashboard
      </a>
    </li>
    <li>
      <a href="index.php?c=books&a=managePulls" class="btn-nav-warn btn">
        <i class="fa-solid fa-list-check"></i> Peticiones
      </a>
    </li>
    <?php endif; ?>
    <li>
      <div style="position:relative">
        <div class="user-pill" onclick="toggleUserDrop()" id="userPill">
          <div class="avatar"><?= $userInitial ?></div>
          <span class="uname"><?= $username ?></span>
        </div>
        <div class="dropdown-menu-fd" id="userDrop">
          <div class="dd-header">
            <small>Sesión iniciada como</small>
            <strong><?= $username ?></strong>
          </div>
          <a href="index.php?c=main"><i class="fa-solid fa-house"></i> Volver al Inicio</a>
          <a href="index.php?c=account&a=logout"><i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión</a>
        </div>
      </div>
    </li>
  </ul>
</nav>

<main>
  <div class="favorites-header">
    <h1 class="favorites-title"><i class="fa-solid fa-heart" style="color:var(--danger)"></i> Mis Favoritos</h1>
    <p style="color:var(--text-secondary); margin-top: 0.5rem">Los recursos que guardaste para consultar más tarde.</p>
  </div>

  <section class="books-section" style="padding-top: 2rem;">
    <div class="books-grid" id="booksGrid">
    </div>
  </section>
</main>

<footer>
  <p>&copy; <?= date('Y') ?> Biblioteca de Formación Docente</p>
</footer>

<div class="toast-container" id="toastContainer"></div>

<!-- Modal para libros (minimalista) -->
<div class="modal-overlay" id="bookModal" onclick="handleOverlayClick(event,'bookModal')">
  <div class="modal modal-lg" role="dialog">
    <div class="modal-header">
      <h3 class="modal-title-fd">Detalles del libro</h3>
      <button class="modal-close" onclick="closeModal('bookModal')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <div class="modal-book-layout">
        <div>
          <img id="modalCover" src="" class="modal-book-cover">
          <div class="modal-like-row" style="margin-top:.75rem">
            <button class="like-btn-large liked" id="modalLikeBtn" onclick="toggleLike()">
              <i class="fa-solid fa-heart"></i>
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
          </ul>
          <p class="modal-book-desc" id="modalDesc">–</p>
        </div>
      </div>
    </div>
    <div class="modal-footer-fd">
      <a id="modalResumeBtn" href="#" target="_blank" class="btn btn-primary" style="display:none">Ver resumen</a>
      <a id="modalPdfBtn" href="#" target="_blank" class="btn btn-glass" style="display:none">Descargar PDF</a>
      <button class="btn btn-glass" onclick="closeModal('bookModal')">Cerrar</button>
    </div>
  </div>
</div>

<script>
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
    'user_liked'  => (bool)($b['user_liked'] ?? false)
  ];
}, $libros ?? [])) ?>;

const IS_LOGGED_IN = true;
let activeBookId = null;

function escHtml(str) {
  const d = document.createElement('div');
  d.textContent = String(str ?? '');
  return d.innerHTML;
}

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
function toggleUserDrop() {
  document.getElementById('userDrop').classList.toggle('open');
}

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

function renderBooks() {
  const grid = document.getElementById('booksGrid');
  grid.innerHTML = '';

  if (!ALL_BOOKS.length) {
    grid.innerHTML = `
      <div class="empty-state" style="grid-column: 1 / -1; display:flex; flex-direction:column; align-items:center; padding: 4rem; color:var(--text-secondary)">
        <i class="fa-regular fa-heart" style="font-size: 3rem; margin-bottom: 1rem;"></i>
        <h3>Aún no tenés favoritos</h3>
        <p style="margin-bottom:1.5rem">Explorá el catálogo principal y marcá con "Me gusta" los libros que te interesen.</p>
        <a href="index.php?c=main" class="btn btn-primary"><i class="fa-solid fa-book-open"></i> Ir al catálogo</a>
      </div>`;
    return;
  }

  ALL_BOOKS.forEach((book, i) => {
    const card = document.createElement('div');
    card.className = 'book-card';
    card.style.animationDelay = `${Math.min(i * 0.04, 0.4)}s`;
    card.innerHTML = `
      <div class="book-cover-wrap">
        <img class="book-cover" src="${escHtml(book.image_path)}" alt="${escHtml(book.title)}" onerror="this.src='assets/img/book_placeholder.png'">
        <button class="card-like-btn liked" onclick="toggleLikeCard(event,${book.id},this)">
          <i class="fa-solid fa-heart"></i>
        </button>
      </div>
      <div class="book-info">
        <h3 class="book-title-card">${escHtml(book.title)}</h3>
        <p class="book-author-card">${escHtml(book.author)}</p>
        <div class="book-meta-row">
          <span class="book-genre-tag">${escHtml(book.genre || 'General')}</span>
          <span class="book-likes-count"><i class="fa-solid fa-heart"></i> ${book.likes}</span>
        </div>
      </div>`;
    card.addEventListener('click', e => {
      if (e.target.closest('.card-like-btn')) return;
      openBookDetail(book.id);
    });
    grid.appendChild(card);
  });
}

function openBookDetail(bookId) {
  const book = ALL_BOOKS.find(b => b.id === bookId);
  if (!book) return;
  activeBookId = bookId;

  document.getElementById('modalCover').src = book.image_path;
  document.getElementById('modalTitle').textContent = book.title;
  document.getElementById('modalAuthor').textContent = book.author;
  document.getElementById('modalYear').textContent = book.year || '–';
  document.getElementById('modalGenre').textContent = book.genre || '–';
  document.getElementById('modalDesc').textContent = book.description || 'Sin descripción';
  document.getElementById('modalLikeCount').textContent = book.likes;

  const likeBtn = document.getElementById('modalLikeBtn');
  likeBtn.className = 'like-btn-large' + (book.user_liked ? ' liked' : '');
  likeBtn.querySelector('i').className = (book.user_liked ? 'fa-solid' : 'fa-regular') + ' fa-heart';

  if (book.url_resumen) { document.getElementById('modalResumeBtn').href = book.url_resumen; document.getElementById('modalResumeBtn').style.display = ''; }
  else document.getElementById('modalResumeBtn').style.display = 'none';

  if (book.pdf_path) { document.getElementById('modalPdfBtn').href = book.pdf_path; document.getElementById('modalPdfBtn').style.display = ''; }
  else document.getElementById('modalPdfBtn').style.display = 'none';

  openModal('bookModal');
}

async function sendLike(bookId) {
  try {
    const res = await fetch(`index.php?c=books&a=toggleLike`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `book_id=${bookId}`
    });
    return await res.json();
  } catch { return null; }
}

async function toggleLike() {
  const book = ALL_BOOKS.find(b => b.id === activeBookId);
  const result = await sendLike(activeBookId);
  if (!result) return;
  book.user_liked = result.liked;
  book.likes = result.count;
  
  if (!result.liked) {
     location.reload(); // If unliked in favorites view, reload to remove it
  }
}

async function toggleLikeCard(e, bookId, btn) {
  e.stopPropagation();
  const book = ALL_BOOKS.find(b => b.id === bookId);
  const result = await sendLike(bookId);
  if (!result) return;
  
  if (!result.liked) {
     const card = btn.closest('.book-card');
     card.style.opacity = '0.5';
     setTimeout(() => location.reload(), 500); // Reload to reflect item removed from favorites
  }
}

document.addEventListener('DOMContentLoaded', () => {
  renderBooks();
});
</script>
</body>
</html>
