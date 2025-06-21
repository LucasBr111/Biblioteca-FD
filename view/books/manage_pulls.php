<?php


// Asegurarse de que la sesión esté iniciada para acceder a $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Asegúrate de que $pulls y $csrf_token estén disponibles (pasados por el controlador)
// Si no, esto generaría un error de variable indefinida.
// El extract($data) en el controlador `render` se encarga de esto.
// $pulls = $pulls ?? []; // Esto es para evitar errores si $pulls no se pasó, pero el controlador debería hacerlo.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Solicitudes de Libros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../css/style.css"> <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
        }
        .container-fluid {
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: #34495e;
            color: white;
            border-bottom: none;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table img {
            max-width: 60px;
            height: auto;
            border-radius: 0.25rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .action-buttons .btn {
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <h1 class="text-center mb-4 text-primary"><i class="fas fa-book-reader me-2"></i>Gestión de Solicitudes de Libros</h1>
        <p class="text-center text-muted">Aquí puedes revisar y decidir qué libros se publican en la biblioteca.</p>

        <div class="card">
            <div class="card-header">
                Pulls Pendientes (Libros sin Publicar)
            </div>
            <div class="card-body">
                <?php if (empty($pulls)): ?>
                    <div class="alert alert-info text-center" role="alert">
                        No hay solicitudes de libros pendientes en este momento. ¡Todo está al día!
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Año</th>
                                    <th>Portada</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pulls as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['id']); ?></td>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['year']); ?></td>
                                    <td>
                                        <?php if (!empty($book['image_path']) && file_exists($book['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Portada" class="img-fluid">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="action-buttons">
                                        <a href="#" class="btn btn-success btn-sm accept-pull-btn" data-id="<?php echo $book['id']; ?>" title="Aceptar y Publicar">
                                            <i class="fas fa-check"></i> Publicar
                                        </a>
                                        <a href="#" class="btn btn-danger btn-sm deny-pull-btn" data-id="<?php echo $book['id']; ?>" title="Denegar y Eliminar">
                                            <i class="fas fa-times"></i> Denegar
                                        </a>
                                        <a href="<?php echo htmlspecialchars($book['pdf_path']); ?>" class="btn btn-secondary btn-sm" target="_blank" title="Ver PDF Temporal">
                                            <i class="fas fa-file-pdf"></i> Ver PDF
                                        </a>
                                        <?php if (!empty($book['summary_url'])): ?>
                                            <a href="<?php echo htmlspecialchars($book['summary_url']); ?>" class="btn btn-info btn-sm" target="_blank" title="Ver Resumen">
                                                <i class="fas fa-eye"></i> Resumen
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <a href="index.php?c=main" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Volver al Inicio</a>
            </div>
        </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lógica para botones de Aceptar/Denegar con SweetAlert de confirmación
            document.querySelectorAll('.accept-pull-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bookId = this.dataset.id;
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡El libro será publicado y movido a su ubicación final!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, publicar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `index.php?c=books&a=acceptPull&id=${bookId}&csrf_token=<?php echo htmlspecialchars($csrf_token); ?>`;
                        }
                    });
                });
            });

            document.querySelectorAll('.deny-pull-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const bookId = this.dataset.id;
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡El libro y sus archivos temporales serán eliminados permanentemente!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, denegar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `index.php?c=books&a=denyPull&id=${bookId}&csrf_token=<?php echo htmlspecialchars($csrf_token); ?>`;
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>