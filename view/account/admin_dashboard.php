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
    .dashboard-header {
      padding: 6rem 5% 2rem;
      background: linear-gradient(to bottom, #001f3f, var(--bg-main));
    }
    .stats-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      padding: 2rem 5%;
    }
    .stat-card {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius);
      padding: 1.5rem;
      text-align: center;
      transition: transform 0.2s;
    }
    .stat-card:hover { border-color: var(--accent); transform: translateY(-3px); }
    .stat-icon { font-size: 2.5rem; color: var(--accent); margin-bottom: 0.5rem; }
    .stat-value { font-size: 2rem; font-weight: 700; color: #fff; font-family: 'Sora', sans-serif;}
    .stat-label { font-size: 0.9rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px;}
    
    .panel-section { padding: 2rem 5%; margin-bottom: 2rem; }
    .panel-title { font-family: 'Sora', sans-serif; margin-bottom: 1rem; color: #fff; display: flex; align-items: center; gap: 0.5rem;}
    
    .table-wrapper {
      background: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: var(--radius);
      overflow-x: auto;
    }
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th, td { padding: 1rem; border-bottom: 1px solid var(--border-color); }
    th { background: rgba(0,0,0,0.2); font-weight: 600; color: var(--text-secondary); cursor: pointer; user-select: none;}
    th:hover { color: #fff;}
    td { color: var(--text-primary); }
    tr:hover td { background: rgba(255,255,255,0.03); }
    .badge { padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
    .badge-yes { background: rgba(0,200,83,0.1); color: #00e676; border: 1px solid rgba(0,200,83,0.3); }
    .badge-no { background: rgba(255,193,7,0.1); color: #ffc107; border: 1px solid rgba(255,193,7,0.3); }
  </style>
</head>
<body>

<nav class="top-nav scrolled" id="topNav" style="background: rgba(0, 5, 10, 0.95)">
  <a class="nav-brand" href="index.php?c=main">
    <img src="assets/img/logo_fd.png" alt="Logo FD" onerror="this.style.display='none'">
    <span>Biblioteca FD</span>
  </a>
  <ul class="nav-links">
    <li><a href="index.php?c=main"><i class="fa-solid fa-house"></i> Inicio</a></li>
    <li><a href="index.php?c=books&a=managePulls" class="btn-nav-warn btn"><i class="fa-solid fa-list-check"></i> Peticiones</a></li>
  </ul>
</nav>

<div class="dashboard-header">
  <h1 style="color:#fff; font-family:'Sora',sans-serif;font-size:2.5rem;"><i class="fa-solid fa-chart-pie" style="color:var(--accent);"></i> Dashboard Admin</h1>
  <p style="color:var(--text-secondary);">Métricas generales y administración de la plataforma.</p>
</div>

<div class="stats-container">
  <div class="stat-card">
    <i class="fa-solid fa-users stat-icon"></i>
    <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
    <div class="stat-label">Total Usuarios</div>
  </div>
  <div class="stat-card">
    <i class="fa-solid fa-book stat-icon"></i>
    <div class="stat-value"><?= number_format($stats['total_books']) ?></div>
    <div class="stat-label">Libros Totales</div>
  </div>
  <div class="stat-card">
    <i class="fa-solid fa-heart stat-icon" style="color:var(--danger)"></i>
    <div class="stat-value"><?= number_format($stats['total_likes']) ?></div>
    <div class="stat-label">Me gustas datos</div>
  </div>
</div>

<div class="panel-section">
  <h2 class="panel-title"><i class="fa-solid fa-users"></i> Insights de Usuarios</h2>
  <div class="table-wrapper">
    <table id="usersTable">
      <thead>
        <tr>
          <th onclick="sortTable('usersTable', 0)">Usuario <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('usersTable', 1)">Email <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('usersTable', 2, true)">Libros Subidos <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('usersTable', 3, true)">Likes Recibidos <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th>Rol</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['books_count'] ?></td>
          <td><i class="fa-solid fa-heart" style="color:var(--danger);font-size:0.8em"></i> <?= $u['total_likes_received'] ?></td>
          <td><?= $u['is_admin'] ? '<span class="badge badge-admin"><i class="fa-solid fa-star"></i> Admin</span>' : '<span class="badge badge-yes" style="color:var(--text-muted);border-color:var(--border-color);background:transparent">Usuario</span>' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="panel-section">
  <h2 class="panel-title"><i class="fa-solid fa-book"></i> Insights de Libros</h2>
  <div class="table-wrapper">
    <table id="booksTable">
      <thead>
        <tr>
          <th onclick="sortTable('booksTable', 0)">Título <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('booksTable', 1)">Autor <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('booksTable', 2)">Subido por <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th onclick="sortTable('booksTable', 3, true)">Likes <i class="fa-solid fa-sort" style="font-size:0.7em"></i></th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="booksTbody">
        <?php foreach ($books as $b): ?>
        <tr class="book-row" data-id="<?= $b['id'] ?>" data-title="<?= htmlspecialchars($b['title']) ?>" data-author="<?= htmlspecialchars($b['author']) ?>" data-year="<?= $b['year'] ?>" data-genre="<?= htmlspecialchars($b['genre']) ?>" data-description="<?= htmlspecialchars($b['description']) ?>">
          <td><strong><?= htmlspecialchars($b['title']) ?></strong></td>
          <td><?= htmlspecialchars($b['author']) ?></td>
          <td><?= htmlspecialchars($b['uploader_username'] ?? 'Sistema') ?></td>
          <td><i class="fa-solid fa-heart" style="color:var(--danger);font-size:0.8em"></i> <?= $b['likes'] ?></td>
          <td><?= $b['publicado'] === 'si' ? '<span class="badge badge-yes">Publicado</span>' : '<span class="badge badge-no">Pendiente</span>' ?></td>
          <td>
            <button class="btn btn-sm" style="padding:0.3rem 0.6rem; font-size:0.8rem" onclick="openEditModal(this)"><i class="fa-solid fa-pen"></i> Editar</button>
            <a href="index.php?c=books&a=delete&id=<?= $b['id'] ?>" onclick="return confirm('¿Eliminar este libro permanentemente?')" class="btn btn-danger btn-sm" style="padding:0.3rem 0.6rem; font-size:0.8rem; display:inline-block; margin-left:4px;"><i class="fa-solid fa-trash"></i></a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="pagination-controls" style="margin-top: 1rem; display: flex; justify-content: space-between; align-items: center; color: var(--text-secondary);">
    <span id="pageInfo">Página 1</span>
    <div>
        <button id="prevBtn" class="btn btn-glass" onclick="changePage(-1)" disabled><i class="fa-solid fa-chevron-left"></i> Anterior</button>
        <button id="nextBtn" class="btn btn-glass" onclick="changePage(1)">Siguiente <i class="fa-solid fa-chevron-right"></i></button>
    </div>
  </div>
</div>

<!-- Edit Book Modal -->
<div class="modal-overlay" id="editModal" onclick="if(event.target===this) document.getElementById('editModal').classList.remove('open')">
  <div class="modal" role="dialog" style="max-width: 500px">
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

<footer>
  <p>&copy; <?= date('Y') ?> Biblioteca de Formación Docente</p>
</footer>

<script>
// Pagination logic
const rowsPerPage = 10;
let currentPage = 1;

function displayTablePage() {
    const table = document.getElementById("booksTable");
    // Get visible rows only (excluding header)
    const rows = Array.from(table.rows).slice(1);
    const totalPages = Math.ceil(rows.length / rowsPerPage);
    
    // Ensure current page is valid
    if (currentPage < 1) currentPage = 1;
    if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    rows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });

    document.getElementById("pageInfo").innerText = `Página ${currentPage} de ${Math.max(1, totalPages)} (Total: ${rows.length})`;
    document.getElementById("prevBtn").disabled = currentPage === 1;
    document.getElementById("nextBtn").disabled = currentPage >= totalPages;
}

function changePage(delta) {
    currentPage += delta;
    displayTablePage();
}

// Edit Modal Logic
function openEditModal(btn) {
    const row = btn.closest('tr');
    document.getElementById('edit_book_id').value = row.dataset.id;
    document.getElementById('edit_title').value = row.dataset.title;
    document.getElementById('edit_author').value = row.dataset.author;
    document.getElementById('edit_year').value = row.dataset.year;
    document.getElementById('edit_genre').value = row.dataset.genre;
    document.getElementById('edit_description').value = row.dataset.description;
    
    const modal = document.getElementById('editModal');
    modal.classList.add('open');
}

function sortTable(tableId, n, isNumeric=false) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById(tableId);
  switching = true;
  dir = "asc"; 
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      
      let xval = isNumeric ? parseInt(x.innerText.replace(/[^0-9-]/g, '')) : x.innerHTML.toLowerCase();
      let yval = isNumeric ? parseInt(y.innerText.replace(/[^0-9-]/g, '')) : y.innerHTML.toLowerCase();

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
  // Reset pagination to page 1 after sorting
  currentPage = 1;
  displayTablePage();
}

// Initialize pagination on load
document.addEventListener('DOMContentLoaded', () => {
    displayTablePage();
});
</script>
</body>
</html>
