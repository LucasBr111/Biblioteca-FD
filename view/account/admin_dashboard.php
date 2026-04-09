<?php
// view/account/admin_dashboard.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | Biblioteca FD</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <style>
    :root {
        --accent: #30b1e4;
        --bg-card: #112240;
        --border-color: rgba(255,255,255,0.1);
        --text-secondary: #8892b0;
    }

    body {
        margin: 0;
        font-family: 'DM Sans', sans-serif;
        background-color: #0a192f;
        color: #fff;
    }

    .dashboard-header {
      padding: 5rem 5% 1.5rem;
      background: linear-gradient(to bottom, #001f3f, #0a192f);
    }

    /* Stats: Deslizables en móvil */
    .stats-container {
      display: flex;
      overflow-x: auto;
      gap: 1rem;
      padding: 1rem 5%;
      scrollbar-width: none;
    }
    .stats-container::-webkit-scrollbar { display: none; }

    .stat-card {
      flex: 1;
      min-width: 120px;
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 12px;
      padding: 1rem;
      text-align: center;
    }
    .stat-icon { font-size: 1.5rem; color: var(--accent); margin-bottom: 0.3rem; }
    .stat-value { font-size: 1.2rem; font-weight: 700; color: #fff; }
    .stat-label { font-size: 0.7rem; color: var(--text-secondary); text-transform: uppercase; }

    /* Buscador */
    .search-wrapper {
        margin-bottom: 1rem;
    }
    .search-input {
        width: 100%;
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        padding: 0.8rem 1rem;
        border-radius: 8px;
        color: #fff;
        font-family: 'DM Sans', sans-serif;
        box-sizing: border-box;
    }
    .search-input:focus { border-color: var(--accent); outline: none; }

    .panel-section { padding: 1rem 5%; margin-bottom: 2rem; }
    .panel-title { font-family: 'Sora', sans-serif; margin-bottom: 1rem; color: #fff; font-size: 1.2rem; }

    /* Optimización de Tablas: Scroll Horizontal Compacto */
    .table-wrapper {
      background: var(--bg-card);
      border-radius: 12px;
      overflow-x: auto; 
      border: 1px solid var(--border-color);
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
        min-width: 600px; 
    }
    
    th, td { 
        padding: 0.8rem 1rem; 
        border-bottom: 1px solid var(--border-color); 
        text-align: left; 
        font-size: 0.9rem;
    }
    
    th {
        background: rgba(0,0,0,0.2);
        color: var(--text-secondary);
        font-weight: 600;
        cursor: pointer;
        user-select: none;
        white-space: nowrap;
    }

    th:hover { color: #fff; }
    tr:hover td { background: rgba(255,255,255,0.03); }

    /* Botones y Badges */
    .btn { padding: 0.5rem 1rem; border-radius: 6px; border: none; cursor: pointer; color: #fff; }
    .btn-sm { padding: 0.4rem 0.6rem; border-radius: 4px; font-size: 0.8rem; background: rgba(255,255,255,0.1); border: none; color: #fff; cursor: pointer; }
    .btn-sm:hover { background: rgba(255,255,255,0.2); }
    .btn-danger { background: rgba(255, 77, 77, 0.2); color: #ff4d4d; }
    .btn-danger:hover { background: rgba(255, 77, 77, 0.4); }
    .btn-glass { background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); }
    .btn-glass:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-primary { background: var(--accent); color: #0a192f; font-weight: bold; }
    
    /* Paginación */
    .pagination-controls {
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    /* Modal */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.8);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal {
        background: var(--bg-card);
        padding: 2rem;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        border: 1px solid var(--border-color);
    }
    .modal-header { display: flex; justify-content: space-between; margin-bottom: 1.5rem; }
    .modal-title-fd { margin: 0; font-family: 'Sora', sans-serif; font-size: 1.2rem; }
    .modal-close { background: none; border: none; color: #fff; font-size: 1.2rem; cursor: pointer; }
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; color: var(--text-secondary); }
    .form-control { width: 100%; padding: 0.8rem; background: rgba(0,0,0,0.2); border: 1px solid var(--border-color); border-radius: 6px; color: #fff; box-sizing: border-box; }
    .form-row { display: flex; gap: 1rem; }
    .form-row .form-group { flex: 1; }
    .modal-footer-fd { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; }

  </style>
</head>
<body>

<nav class="top-nav scrolled" id="topNav" style="background: rgba(0, 5, 10, 0.95); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center;">
  <a class="nav-brand" href="index.php?c=main" style="text-decoration: none;">
    <span style="color:var(--accent); font-weight:bold; font-family:'Sora', sans-serif; font-size: 1.2rem;">Biblioteca FD</span>
  </a>
  <a href="index.php?c=books&a=managePulls" style="color:#fff; text-decoration:none; font-size: 1.2rem;"><i class="fa-solid fa-list-check"></i></a>
</nav>

<div class="dashboard-header">
  <h1 style="color:#fff; font-family:'Sora',sans-serif;font-size:1.8rem; margin:0;"><i class="fa-solid fa-chart-pie" style="color:var(--accent);"></i> Dashboard</h1>
</div>

<div class="stats-container">
  <div class="stat-card">
    <i class="fa-solid fa-users stat-icon"></i>
    <div class="stat-value"><?= number_format($stats['total_users'] ?? 0) ?></div>
    <div class="stat-label">Users</div>
  </div>
  <div class="stat-card">
    <i class="fa-solid fa-book stat-icon"></i>
    <div class="stat-value"><?= number_format($stats['total_books'] ?? 0) ?></div>
    <div class="stat-label">Libros</div>
  </div>
  <div class="stat-card">
    <i class="fa-solid fa-heart stat-icon" style="color:#ff4d4d"></i>
    <div class="stat-value"><?= number_format($stats['total_likes'] ?? 0) ?></div>
    <div class="stat-label">Likes</div>
  </div>
</div>

<div class="panel-section">
  <h2 class="panel-title"><i class="fa-solid fa-users"></i> Insights de Usuarios</h2>
  
  <div class="search-wrapper">
    <input type="text" id="userSearch" class="search-input" placeholder="🔍 Buscar usuario por nombre o email..." onkeyup="filterUsers()">
  </div>

  <div class="table-wrapper">
    <table id="usersTable">
      <thead>
        <tr>
          <th onclick="sortTable('usersTable', 0)">Usuario <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('usersTable', 1)">Email <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('usersTable', 2, true)">Libros <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('usersTable', 3, true)">Likes <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th>Rol</th>
        </tr>
      </thead>
      <tbody id="usersTbody">
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $u): ?>
            <tr class="user-row" 
                data-username="<?= strtolower(htmlspecialchars($u['username'])) ?>"
                data-email="<?= strtolower(htmlspecialchars($u['email'])) ?>">
              <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><?= $u['books_count'] ?></td>
              <td><i class="fa-solid fa-heart" style="color:#ff4d4d"></i> <?= $u['total_likes_received'] ?></td>
              <td>
                  <?= $u['is_admin'] ? '<span style="padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; background:rgba(48,177,228,0.2); color:var(--accent);">Admin</span>' : '<span style="padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; background:rgba(255,255,255,0.1); color:var(--text-secondary);">Usuario</span>' ?>
              </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align: center;">No hay usuarios registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <div class="pagination-controls" id="user-pagination-container">
    <span id="userPageInfo">Página 1</span>
    <div style="display: flex; gap: 10px;">
        <button id="userPrevBtn" class="btn btn-glass" onclick="changeUserPage(-1)" disabled><i class="fa-solid fa-chevron-left"></i> Ant</button>
        <button id="userNextBtn" class="btn btn-glass" onclick="changeUserPage(1)">Sig <i class="fa-solid fa-chevron-right"></i></button>
    </div>
  </div>
</div>

<div class="panel-section">
  <h2 class="panel-title"><i class="fa-solid fa-book"></i> Gestión de Libros</h2>
  
  <div class="search-wrapper">
    <input type="text" id="bookSearch" class="search-input" placeholder="🔍 Buscar libro por título, autor o subidor..." onkeyup="filterBooks()">
  </div>

  <div class="table-wrapper">
    <table id="booksTable">
      <thead>
        <tr>
          <th onclick="sortTable('booksTable', 0)">Título <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('booksTable', 1)">Autor <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('booksTable', 2)">Subido por <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('booksTable', 3, true)">Likes <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="booksTbody">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $b): ?>
            <tr class="book-row" 
                data-id="<?= $b['id'] ?>"
                data-year="<?= $b['year'] ?? '' ?>"
                data-genre="<?= htmlspecialchars($b['genre'] ?? '') ?>"
                data-description="<?= htmlspecialchars($b['description'] ?? '') ?>"
                data-title="<?= strtolower(htmlspecialchars($b['title'])) ?>" 
                data-author="<?= strtolower(htmlspecialchars($b['author'])) ?>"
                data-uploader="<?= strtolower(htmlspecialchars($b['uploader_username'] ?? 'sistema')) ?>">
              <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
              <td><?= htmlspecialchars($b['author']) ?></td>
              <td><?= htmlspecialchars($b['uploader_username'] ?? 'Sistema') ?></td>
              <td><i class="fa-solid fa-heart" style="color:#ff4d4d"></i> <?= $b['likes'] ?></td>
              <td>
                <button class="btn-sm" onclick="openEditModal(this)"><i class="fa-solid fa-pen"></i></button>
                <a href="index.php?c=books&a=delete&id=<?= $b['id'] ?>" class="btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este libro?')"><i class="fa-solid fa-trash"></i></a>
              </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align: center;">No hay libros registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  
  <div class="pagination-controls" id="book-pagination-container">
    <span id="bookPageInfo">Página 1</span>
    <div style="display: flex; gap: 10px;">
        <button id="bookPrevBtn" class="btn btn-glass" onclick="changeBookPage(-1)" disabled><i class="fa-solid fa-chevron-left"></i> Ant</button>
        <button id="bookNextBtn" class="btn btn-glass" onclick="changeBookPage(1)">Sig <i class="fa-solid fa-chevron-right"></i></button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="editModal" onclick="if(event.target===this) document.getElementById('editModal').classList.remove('open')">
  <div class="modal" role="dialog">
    <div class="modal-header">
      <h3 class="modal-title-fd"><i class="fa-solid fa-pen" style="color:var(--accent)"></i> Editar Libro</h3>
      <button class="modal-close" onclick="document.getElementById('editModal').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <form id="editForm" action="index.php?c=books&a=edit" method="POST">
        <input type="hidden" name="book_id" id="edit_book_id">
        <div class="form-group">
          <label class="form-label">Título</label>
          <input type="text" class="form-control" name="title" id="edit_title" required>
        </div>
        <div class="form-group">
          <label class="form-label">Autor</label>
          <input type="text" class="form-control" name="author" id="edit_author" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Año</label>
            <input type="number" class="form-control" name="year" id="edit_year">
          </div>
          <div class="form-group">
            <label class="form-label">Género</label>
            <input type="text" class="form-control" name="genre" id="edit_genre">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Descripción</label>
          <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
        </div>
      </form>
    </div>
    <div class="modal-footer-fd">
      <button class="btn btn-glass" onclick="document.getElementById('editModal').classList.remove('open')">Cancelar</button>
      <button class="btn btn-primary" onclick="document.getElementById('editForm').submit()">Guardar</button>
    </div>
  </div>
</div>

<script>
// CONFIGURACIÓN GENERAL
const rowsPerPage = 10;

// --- LÓGICA DE USUARIOS ---
let userCurrentPage = 1;
let isUserSearching = false;

function displayUserTablePage() {
    if (isUserSearching) return; 

    const table = document.getElementById("usersTable");
    const rows = Array.from(table.rows).slice(1);
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    
    if (userCurrentPage < 1) userCurrentPage = 1;
    if (userCurrentPage > totalPages && totalPages > 0) userCurrentPage = totalPages;

    const start = (userCurrentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

    const pageInfo = document.getElementById("userPageInfo");
    const prevBtn = document.getElementById("userPrevBtn");
    const nextBtn = document.getElementById("userNextBtn");

    if (pageInfo) pageInfo.innerText = `Página ${userCurrentPage} de ${Math.max(1, totalPages)} (Total: ${rows.length})`;
    if (prevBtn) prevBtn.disabled = userCurrentPage === 1;
    if (nextBtn) nextBtn.disabled = userCurrentPage >= totalPages || totalPages === 0;
}

function changeUserPage(delta) {
    userCurrentPage += delta;
    displayUserTablePage();
}

function filterUsers() {
    const input = document.getElementById('userSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    const paginationContainer = document.getElementById('user-pagination-container');
    
    isUserSearching = input.length > 0;

    if (isUserSearching) {
        if (paginationContainer) paginationContainer.style.display = 'none';
        
        rows.forEach(row => {
            const text = (row.getAttribute('data-username') || "") + " " + 
                         (row.getAttribute('data-email') || "");
            
            if (text.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    } else {
        if (paginationContainer) paginationContainer.style.display = 'flex';
        userCurrentPage = 1;
        displayUserTablePage();
    }
}

// --- LÓGICA DE LIBROS ---
let bookCurrentPage = 1;
let isBookSearching = false;

function displayBookTablePage() {
    if (isBookSearching) return; 

    const table = document.getElementById("booksTable");
    const rows = Array.from(table.rows).slice(1);
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    
    if (bookCurrentPage < 1) bookCurrentPage = 1;
    if (bookCurrentPage > totalPages && totalPages > 0) bookCurrentPage = totalPages;

    const start = (bookCurrentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

    const pageInfo = document.getElementById("bookPageInfo");
    const prevBtn = document.getElementById("bookPrevBtn");
    const nextBtn = document.getElementById("bookNextBtn");

    if (pageInfo) pageInfo.innerText = `Página ${bookCurrentPage} de ${Math.max(1, totalPages)} (Total: ${rows.length})`;
    if (prevBtn) prevBtn.disabled = bookCurrentPage === 1;
    if (nextBtn) nextBtn.disabled = bookCurrentPage >= totalPages || totalPages === 0;
}

function changeBookPage(delta) {
    bookCurrentPage += delta;
    displayBookTablePage();
}

function filterBooks() {
    const input = document.getElementById('bookSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.book-row');
    const paginationContainer = document.getElementById('book-pagination-container');
    
    isBookSearching = input.length > 0;

    if (isBookSearching) {
        if (paginationContainer) paginationContainer.style.display = 'none';
        
        rows.forEach(row => {
            const text = (row.getAttribute('data-title') || "") + " " + 
                         (row.getAttribute('data-author') || "") + " " + 
                         (row.getAttribute('data-uploader') || "");
            
            if (text.includes(input)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    } else {
        if (paginationContainer) paginationContainer.style.display = 'flex';
        bookCurrentPage = 1;
        displayBookTablePage();
    }
}

// --- LÓGICA COMPARTIDA (SORT Y MODAL) ---
function sortTable(tableId, n, isNumeric=false) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById(tableId);
  switching = true;
  dir = "asc"; 
  
  // Limpiar búsqueda si se ordena la tabla correspondiente
  if(tableId === 'booksTable') {
      const searchInput = document.getElementById('bookSearch');
      if(searchInput && searchInput.value.length > 0) {
          searchInput.value = "";
          filterBooks();
      }
  } else if (tableId === 'usersTable') {
      const searchInput = document.getElementById('userSearch');
      if(searchInput && searchInput.value.length > 0) {
          searchInput.value = "";
          filterUsers();
      }
  }

  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      
      let xval = isNumeric ? parseInt(x.innerText.replace(/[^0-9-]/g, '') || 0) : x.innerText.toLowerCase();
      let yval = isNumeric ? parseInt(y.innerText.replace(/[^0-9-]/g, '') || 0) : y.innerText.toLowerCase();

      if (dir == "asc") {
        if (xval > yval) { shouldSwitch = true; break; }
      } else if (dir == "desc") {
        if (xval < yval) { shouldSwitch = true; break; }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++;
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
  
  // Reiniciar la paginación correcta
  if(tableId === 'booksTable') {
      bookCurrentPage = 1;
      displayBookTablePage();
  } else if (tableId === 'usersTable') {
      userCurrentPage = 1;
      displayUserTablePage();
  }
}

function openEditModal(btn) {
    const row = btn.closest('tr');
    
    const setVal = (id, val) => { 
        let el = document.getElementById(id); 
        if(el) el.value = val; 
    };

    setVal('edit_book_id', row.getAttribute('data-id'));
    setVal('edit_title', row.cells[0].innerText.trim());
    setVal('edit_author', row.cells[1].innerText.trim());
    setVal('edit_year', row.getAttribute('data-year'));
    setVal('edit_genre', row.getAttribute('data-genre'));
    setVal('edit_description', row.getAttribute('data-description'));
    
    const modal = document.getElementById('editModal');
    if(modal) modal.classList.add('open');
}

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', () => {
    displayUserTablePage();
    displayBookTablePage();
});
</script>
</body>
</html>