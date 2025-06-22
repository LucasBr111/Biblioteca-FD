<?php
// main.php

// Asegúrate de que la sesión esté iniciada aquí
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generar el token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Determinar si el usuario es administrador
$userAdmin = (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true);

// Determinar si el usuario está logueado para el dropdown del usuario
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username'] ?? 'Usuario') : 'Invitado';

// Recuperar los valores de búsqueda y ordenamiento para pre-rellenar el formulario
// NOTA: Estas variables ya no se usan directamente para el filtrado en PHP,
// pero las mantenemos por si decides volver a un enfoque híbrido o para depuración.
// Para el filtrado en cliente, los valores iniciales se configuran en JS.

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca de Formación Docente</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Estilos adicionales para el dropdown de usuario en el offcanvas */
        .user-dropdown {
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            background-color: #1e3a5f;
            color: #ffffff !important;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .user-dropdown:hover {
        background-color: #2c3e50;
        color: #ffffff !important;
    }
        .user-dropdown .user-avatar {
            font-size: 1.5rem; /* Tamaño del icono de usuario */
            color:rgb(255, 255, 255);
        }
        .user-dropdown .fas.fa-chevron-down {
            margin-left: auto; /* Empuja la flecha a la derecha */
        }
        .dropdown-menu-offcanvas {
            border: 1px solid #e0e0e0;
            width: calc(100% - 2rem); /* Ajusta el ancho para que coincida con el offcanvas */
            margin-left: 1rem; /* Margen para que se alinee */
            margin-right: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
            margin-top: 0.5rem !important; /* Asegura que no haya espacio extra */
        }
        .dropdown-menu-offcanvas .dropdown-header {
            padding: 0.75rem 1.5rem;
            font-size: 0.9rem;
            white-space: normal; /* Permite que el texto se ajuste */
            font-weight: 600;
            color: #343a40;
            background-color: #f8f9fa;
        }
        .dropdown-menu-offcanvas .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-weight: 500;        
        }
        .dropdown-menu-offcanvas .dropdown-item:hover {
        background-color: #f1f1f1;
        border-radius: 8px;
    }

        .navbar.fixed-top {
            background-color: #001c3b;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .btn {
        border-radius: 8px;
        font-weight: 600;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }

    .btn-info:hover {
        background-color: #117a8b;
        border-color: #117a8b;
    }

    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #e0a800;
    }

    .navbar-brand img {
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover img {
        transform: rotate(5deg);
    }

    .nav-link {
        color: #ffffff !important;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .nav-link:hover {
        color: #ffd700 !important; /* Dorado para resaltar */
    }

    .custom-btn {
        border-radius: 30px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        letter-spacing: 1px;
    }

    .custom-btn:hover {
        transform: scale(1.05);
    }

    /* Estilos para el formulario de búsqueda y filtros */
    .search-filter-section {
        background-color: var(--background-light); /* Asume que --background-light está definido en style.css */
        padding: 2rem 0;
        border-bottom: 1px solid #dee2e6;
    }
    .search-filter-form .form-control,
    .search-filter-form .form-select {
        border-radius: 0.5rem;
    }
    .search-filter-form .btn {
        border-radius: 0.5rem;
    }

    /* Mejora visual para la sección de búsqueda */
.search-filter-section {
    background-color: #f8f9fa; /* Gris claro para fondo */
    padding: 1rem 0;
    border-bottom: 1px solid #dee2e6;
}

.search-filter-form .form-control,
.search-filter-form .form-select {
    border-radius: 8px; /* Redondeado suave */
    font-size: 0.9rem; /* Tamaño de fuente ligeramente reducido */
}

.search-filter-form .btn {
    border-radius: 8px; /* Redondeado consistente */
}

#searchButton {
    background-color: #007bff; /* Azul primario */
    border-color: #007bff;
}

#searchButton:hover {
    background-color: #0056b3; /* Sombra más oscura al hover */
    border-color: #0056b3;
}

#applyFiltersButton {
    background-color: #6c757d; /* Gris oscuro */
    border-color: #6c757d;
}

#applyFiltersButton:hover {
    background-color: #5a6268; /* Sombra más oscura al hover */
    border-color: #5a6268;
}
    </style>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php?c=main">
                    <img src="assets/img/logo_fd.png" class="me-2" style="height: 50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?c=main">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#libros">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="btn btn-primary me-lg-2 mt-2 mt-lg-0" data-bs-toggle="modal" data-bs-target="#createSummaryModal">
                                <i class="fas fa-magic me-2"></i>Crear Resumen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" id="btnCargarLibro" class="btn btn-info mt-2 mt-lg-0">
                                <i class="fas fa-upload me-2"></i>Cargar Libro
                            </a>
                        </li>
                        <?php if ($userAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-warning text-white ms-lg-2 mt-2 mt-lg-0" href="index.php?c=books&a=managePulls">
                                <i class="fas fa-tasks me-2"></i>Ver Peticiones
                            </a>
                        </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown d-none d-lg-block">
                            <a class="nav-link dropdown-toggle user-dropdown d-flex align-items-center" href="#" id="userDropdownDesktop" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding-top: 0px; padding-bottom: 0px; margin-left: 15px; margin-top: 15px;">
                                <?php if ($isLoggedIn): ?>
                                    <div class="user-avatar me-2">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2"><?php echo $username; ?></span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="user-avatar me-2">
                                        <i class="fas fa-user-circle"></i>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Invitado</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownDesktop" >
                                <?php if ($isLoggedIn): ?>
                                    <div class="dropdown-header">
                                        Sesión iniciada como<br>
                                        <strong><?php echo $username; ?></strong>
                                    </div>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="index.php?c=account&a=logout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                                <?php else: ?>
                                    <div class="dropdown-header">No has iniciado sesión</div>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="index.php?c=account&a=loginForm"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</a></li>
                                    <li><a class="dropdown-item" href="index.php?c=account&a=registerForm"><i class="fas fa-user-plus me-2"></i>Registrarse</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menú de Navegación</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item dropdown w-100 mb-3">
                        <a class="nav-link dropdown-toggle user-dropdown d-flex align-items-center" href="#" id="userDropdownOffcanvas" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <?php if ($isLoggedIn): ?>
                                <div class="user-avatar me-2">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="me-2"><?php echo $username; ?></span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            <?php else: ?>
                                <div class="user-avatar me-2">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="me-2">Invitado</span>
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-offcanvas" aria-labelledby="userDropdownOffcanvas">
                            <?php if ($isLoggedIn): ?>
                                <div class="dropdown-header">
                                    Sesión iniciada como<br>
                                    <strong><?php echo $username; ?></strong>
                                </div>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?c=account&a=profile" data-bs-dismiss="offcanvas"><i class="fas fa-id-card me-2"></i>Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="index.php?c=account&a=settings" data-bs-dismiss="offcanvas"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?c=account&a=logout" data-bs-dismiss="offcanvas"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                            <?php else: ?>
                                <div class="dropdown-header">No has iniciado sesión</div>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?c=account&a=loginForm" data-bs-dismiss="offcanvas"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</a></li>
                                <li><a class="dropdown-item" href="index.php?c=account&a=registerForm" data-bs-dismiss="offcanvas"><i class="fas fa-user-plus me-2"></i>Registrarse</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php?c=main" data-bs-dismiss="offcanvas">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#libros" data-bs-dismiss="offcanvas">Catálogo</a>
                    </li>
                    <li class="nav-item mt-3">
                        <a href="#" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#createSummaryModal" data-bs-dismiss="offcanvas">
                            <i class="fas fa-magic me-2"></i>Crear Resumen
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a href="#" id="btnCargarLibroOffcanvas" class="btn btn-info w-100" data-bs-dismiss="offcanvas">
                            <i class="fas fa-upload me-2"></i>Cargar Libro
                        </a>
                    </li>
                    <?php if ($userAdmin): ?>
                    <li class="nav-item mt-3">
                        <a class="nav-link btn btn-warning text-white w-100" href="index.php?c=books&a=managePulls" data-bs-dismiss="offcanvas">
                            <i class="fas fa-tasks me-2"></i>Ver Peticiones
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <main>
    <section class="hero d-flex align-items-center justify-content-center text-center text-white">
        <div class="hero-content animate_animated animate_fadeInUp">
            <hr>
            <h1 class="display-4 mb-3">Bienvenido a la Biblioteca de Formación Docente</h1>
            <hr>
            <p class="lead mb-4">Dónde el conocimiento guía tu vocación</p>
            
            <a href="#libros" class="btn btn-primary btn-lg custom-btn animate_animated animatepulse animate_infinite">Explorar Catálogo <i class="fas fa-book-open ms-2"></i></a>
        </div>
    </section>

    <!-- Sección de Búsqueda y Filtros -->
<section class="search-filter-section py-3 bg-light">
    <div class="container">
        <form id="filterForm" class="row g-2 align-items-center justify-content-center search-filter-form">
            <!-- Campo de búsqueda -->
            <div class="col-12 col-md-4 col-lg-3">
                <div class="input-group">
                    <input type="search" class="form-control" id="search_query" name="search_query" placeholder="Buscar por título o autor..." aria-label="Buscar">
                    <button type="button" class="btn btn-primary btn-sm" id="searchButton">
                        <i class="fas fa-search me-1"></i>Buscar
                    </button>
                </div>
            </div>

            <!-- Selector de ordenamiento -->
            <div class="col-6 col-md-3 col-lg-2">
                <select class="form-select form-select-sm" id="sort_by" name="sort_by">
                    <option value="created_at">Fecha de Carga</option>
                    <option value="title">Título</option>
                    <option value="author">Autor</option>
                    <option value="year">Año</option>
                </select>
            </div>

            <!-- Orden ascendente/descendente -->
            <div class="col-6 col-md-2 col-lg-1">
                <select class="form-select form-select-sm" id="sort_order" name="sort_order">
                    <option value="DESC">Descendente</option>
                    <option value="ASC">Ascendente</option>
                </select>
            </div>

            <!-- Botón de filtrar -->
            <div class="col-12 col-md-2 col-lg-1 d-grid">
                <button type="button" class="btn btn-secondary btn-sm" id="applyFiltersButton">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
            </div>
        </form>
    </div>
</section>

    <section id="libros" class="books-section py-5">
        <div class="container">
            <h2 class="text-center mb-5 animate_animated animate_fadeInDown">Nuestro Catálogo</h2>
            <div class="books-grid justify-content-center" id="booksGrid">
            <?php if (empty($libros)):?>
                <div class="col-12 text-center py-5">
                    <p class="lead text-muted">No hay libros disponibles en el catálogo en este momento (desde PHP).</p>
                    <i class="fas fa-box-open fa-3x text-secondary mt-3"></i>
                </div>
            <?php endif; ?>
            <!-- Los libros se renderizarán aquí dinámicamente con JavaScript -->
            </div>
        </div>
    </section>
</main>
   

    <footer class="py-5">
        <div class="container">
            <div class="footer-content row justify-content-center text-center text-md-start">
                <div class="footer-info col-md-6 col-lg-4 mb-4 mb-md-0">
                    <h3 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Contacto</h3>
                    <p>Ciudad del Este, Paraguay</p>
                    <p><i class="fas fa-phone-alt me-2"></i>Teléfono: (0971) 234 567</p>
                    <p><i class="fas fa-envelope me-2"></i>Email: info@bibliotecadocente.edu.py</p>
                </div>
            </div>
            <hr class="my-4 border-light">
            <div class="footer-bottom text-center">
                <p class="mb-0">&copy; 2025 Biblioteca de Formación Docente. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="bookDetailModal" tabindex="-1" aria-labelledby="bookDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookDetailModalLabel">Detalles del Libro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-4 text-center">
                        <img id="modalBookImage" src="" alt="Portada del libro" class="img-fluid rounded shadow-sm mb-3">
                    </div>
                    <div class="col-md-8">
                        <h3 id="modalBookTitle" class="mb-3"></h3>
                        <p class="text-muted mb-2">Autor: <span id="modalBookAuthor"></span></p>
                        <p class="text-muted mb-2">Año: <span id="modalBookYear"></span></p>
                        <p class="text-muted mb-4">Género: <span id="modalBookGenre"></span></p>
                        <p id="modalBookDescription"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <div id="modalFooter" class="d-flex justify-content-end w-100">
                        <a id="modalResumeButton" class="btn btn-primary me-2" style="display: none;">
                            <i class="fas fa-eye me-2"></i>Ver Resumen Completo
                        </a>
                        <a id="modalDownloadButton" class="btn btn-secondary me-2" style="display: none; ">
                            <i class="fas fa-download me-2"></i>Descargar PDF
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadBookModal" tabindex="-1" aria-labelledby="uploadBookModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="uploadBookModalLabel"><i class="fas fa-book-medical me-2"></i>Cargar Nuevo Libro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadBookForm" action="index.php?c=books&a=upload" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="bookTitle" class="form-label">Título del Libro:</label>
                            <input type="text" class="form-control" id="bookTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="bookAuthor" class="form-label">Autor(es):</label>
                            <input type="text" class="form-control" id="bookAuthor" name="author" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bookYear" class="form-label">Año de Publicación:</label>
                                <input type="number" class="form-control" id="bookYear" name="year" min="1000" max="<?= date('Y') + 5; ?>" value="<?= date('Y'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bookGenre" class="form-label">Género/Categoría:</label>
                                <input type="text" class="form-control" id="bookGenre" name="genre">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bookDescription" class="form-label">Descripción:</label>
                            <textarea class="form-control" id="bookDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="bookCover" class="form-label">Imagen de Portada (JPG, PNG, GIF):</label>
                            <input type="file" class="form-control" id="bookCover" name="cover_image" accept="image/jpeg, image/png, image/gif">
                            <small class="form-text text-muted">Tamaño máximo: 2MB</small>
                        </div>
                        <div class="mb-3">
                            <label for="bookPdf" class="form-label">Archivo PDF:</label>
                            <input type="file" class="form-control" id="bookPdf" name="pdf_file" accept="application/pdf" required>
                            <small class="form-text text-muted">Tamaño máximo: 20MB</small>
                        </div>
                        <div class="mb-3">
                            <label for="bookSummaryUrl" class="form-label">URL del Resumen (Opcional):</label>
                            <input type="url" class="form-control" id="bookSummaryUrl" name="summary_url" placeholder="https://ejemplo.com/resumen-libro">
                        </div>

                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Guardar Libro</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createSummaryModal" tabindex="-1" aria-labelledby="createSummaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content summary-modal-content">
                <div class="modal-header summary-modal-header text-white">
                    <h5 class="modal-title" id="createSummaryModalLabel">
                        <i class="fas fa-lightbulb me-2"></i>Guía: Crea un Resumen Mágico de tu Libro
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body summary-modal-body">
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-4 d-flex">
                            <div class="summary-step-card flex-grow-1">
                                <div class="summary-step-icon"><i class="fas fa-robot"></i></div>
                                <h6 class="summary-step-title">Paso 1: ¡El Prompt Especializado!</h6>
                                <p class="summary-step-description">Usa este prompt optimizado para obtener los mejores resúmenes de tus PDFs.</p>
                                <div class="card bg-dark text-white p-3 mb-3 prompt-container">
                                    <code id="summaryPrompt" class="d-block text-start">
                                        "Actúa como un experto en análisis de contenido. Vas a resumir un PDF de un libro. Tu objetivo es extraer las ideas clave, argumentos principales y conclusiones, presentándolos de forma concisa y clara. El resumen debe ser objetivo, cubriendo los puntos más relevantes sin añadir opiniones personales. Proporciona una visión general completa que permita entender el libro sin leerlo por completo. Mantén un tono formal y académico. Asegúrate de incluir los nombres de los autores principales citados si son relevantes y las fechas o contextos históricos importantes. El resumen debe tener entre 500 y 700 palabras."
                                    </code>
                                    <button class="btn btn-outline-info btn-sm mt-2" onclick="copyToClipboard('summaryPrompt', this)">
                                        <i class="fas fa-copy me-2"></i>Copiar Prompt
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4 d-flex">
                            <div class="summary-step-card flex-grow-1">
                                <div class="summary-step-icon"><i class="fas fa-file-pdf"></i></div>
                                <h6 class="summary-step-title">Paso 2: PDF a ChatGPT</h6>
                                <p class="summary-step-description">Ahora, ve a ChatGPT (o tu IA preferida) y carga el archivo PDF del libro que quieres resumir.</p>
                                <p class="summary-step-detail">¡Asegúrate de haber copiado el prompt del Paso 1 antes de esto! Pega el prompt en la conversación con la IA y luego sube tu PDF.</p>
                                <p class="summary-step-detail">Espera a que la IA procese el PDF y genere el resumen basado en el prompt que le diste.</p>
                            </div>
                        </div>

                        <div class="col-md-12 col-lg-4 d-flex">
                            <div class="summary-step-card flex-grow-1">
                                <div class="summary-step-icon"><i class="fas fa-cube"></i></div>
                                <h6 class="summary-step-title">Paso 3: ¡Transforma en Gamma!</h6>
                                <p class="summary-step-description">Una vez que tengas el resumen en texto, es hora de darle vida en Gamma.app.</p>
                                <ul class="list-unstyled text-start summary-list-icons">
                                    <li><i class="fas fa-check-circle text-success me-2"></i>Copia el texto del resumen generado por la IA.</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i>Ve a Gamma.app y usa la opción "Crear nuevo" o "New from text".</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i>Pega tu resumen y sigue los pasos para crear una presentación interactiva.</li>
                                    <li><i class="fas fa-check-circle text-success me-2"></i>Puedes también usar la opción de "Cargar con enlace" si Gamma lo permite, para enlazar directamente tu PDF o el resumen si lo tienes en una URL pública.</li>
                                </ul>
                                <p class="summary-step-detail">Cuando termines, tendrás un resumen visualmente atractivo y compartible.</p>
                                <a href="https://gamma.app/create" target="_blank" class="btn btn-success mt-3 w-100">
                                    <i class="fas fa-external-link-alt me-2"></i>Ir a Gamma.app/create
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer summary-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    

    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.2/dist/sweetalert2.all.min.js"></script>
    <script src="js/libros.js"></script>

    <script>
        // Declaramos allBooks globalmente para que sea accesible
        let allBooks = []; 
        const booksGrid = document.getElementById('booksGrid');
        const searchInput = document.getElementById('search_query');
        const sortBySelect = document.getElementById('sort_by');
        const sortOrderSelect = document.getElementById('sort_order');
        const searchButton = document.getElementById('searchButton');
        const applyFiltersButton = document.getElementById('applyFiltersButton');


        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa allBooks con los datos de PHP solo una vez
            // Asegúrate de que $libros sea un array JSON válido
            allBooks = <?php echo json_encode($libros); ?>;
            
            // *** IMPORTANTE PARA LA DEPURACIÓN ***
            console.log("Contenido inicial de allBooks (desde PHP):", allBooks);

            // Renderiza y aplica filtros/ordenamiento inicial
            // (Esto asegura que los libros se muestren al cargar, y aplica cualquier orden por defecto)
            applyFiltersAndSort();

            // Añade event listeners para búsqueda y filtros
            searchButton.addEventListener('click', applyFiltersAndSort);
            applyFiltersButton.addEventListener('click', applyFiltersAndSort);
            searchInput.addEventListener('keyup', (event) => {
                if (event.key === 'Enter') {
                    applyFiltersAndSort();
                }
            });
            sortBySelect.addEventListener('change', applyFiltersAndSort);
            sortOrderSelect.addEventListener('change', applyFiltersAndSort);


            const catalogoLink = document.querySelector('a[href="#libros"]');
            if (catalogoLink) {
                catalogoLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasNavbar'));
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                });
            }

            // Cierra el offcanvas si se hace clic en cualquier enlace dentro de él
            document.querySelectorAll('#offcanvasNavbar .nav-link:not(.dropdown-toggle), #offcanvasNavbar .btn').forEach(link => {
                link.addEventListener('click', () => {
                    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('offcanvasNavbar'));
                    if (offcanvas) {
                        offcanvas.hide();
                    }
                });
            });

            // LÓGICA DEL BOTÓN "CARGAR LIBRO"
            const btnCargarLibro = document.getElementById('btnCargarLibro');
            const btnCargarLibroOffcanvas = document.getElementById('btnCargarLibroOffcanvas');

            const handleCargarLibroClick = (event) => {
                event.preventDefault();
                <?php if ($isLoggedIn): ?>
                    var uploadModal = new bootstrap.Modal(document.getElementById('uploadBookModal'));
                    uploadModal.show();
                <?php else: ?>
                    Swal.fire({
                        icon: 'warning',
                        title: 'Acceso Restringido',
                        text: 'Necesitas iniciar sesión para cargar un libro.',
                        showCancelButton: true,
                        confirmButtonText: 'Ir a Iniciar Sesión',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php?c=account&a=loginForm';
                        }
                    });
                <?php endif; ?>
            };

            if (btnCargarLibro) {
                btnCargarLibro.addEventListener('click', handleCargarLibroClick);
            }
            if (btnCargarLibroOffcanvas) {
                btnCargarLibroOffcanvas.addEventListener('click', handleCargarLibroClick);
            }

            // SCRIPT PARA COPIAR PROMPT
            function copyToClipboard(elementId, button) {
                var copyText = document.getElementById(elementId);
                var textArea = document.createElement("textarea");
                textArea.value = copyText.textContent;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand("copy");
                textArea.remove();

                var originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check me-2"></i>¡Copiado!';
                button.classList.remove('btn-outline-info');
                button.classList.add('btn-info');

                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-info');
                    button.classList.add('btn-outline-info');
                }, 2000);
            }
            window.copyToClipboard = copyToClipboard;
        });

        /**
         * Renderiza las tarjetas de libros en el DOM.
         * @param {Array} booksToRender - Array de objetos de libros a mostrar.
         */
        function renderBooks(booksToRender) {
            booksGrid.innerHTML = ''; // Limpiar el contenido actual
            if (booksToRender.length === 0) {
                booksGrid.innerHTML = `
                    <div class="col-12 text-center py-5">
                        <p class="lead text-muted">No se encontraron libros que coincidan con tu búsqueda o filtros.</p>
                        <i class="fas fa-box-open fa-3x text-secondary mt-3"></i>
                    </div>
                `;
                return;
            }

            booksToRender.forEach(book => {
                const bookCardHtml = `
                    <div class="book-card-wrapper">
                        <div class="card h-100 book-card animate_animated animate_fadeIn"
                            data-id="${book.id}"
                            data-title="${book.title}"
                            data-author="${book.author}"
                            data-year="${book.year}"
                            data-genre="${book.genre}"
                            data-description="${book.description}"
                            data-image="${book.image_path}"
                            data-pdf="${book.pdf}"
                            data-summary="${book.summary}">
                            <img src="${book.image_path}" class="card-img-top book-image" alt="Portada de ${book.title}">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title book-title">${book.title}</h5>
                                <p class="card-text text-muted book-author">Por: ${book.author}</p>
                                <p class="card-text book-description-short flex-grow-1">${book.description.substring(0, 120)}...</p>
                            </div>
                        </div>
                    </div>
                `;
                booksGrid.innerHTML += bookCardHtml;
            });
        }

        /**
         * Aplica los filtros y el ordenamiento a la lista de libros y los renderiza.
         */
        function applyFiltersAndSort() {
            const searchTerm = searchInput.value.toLowerCase();
            const sortBy = sortBySelect.value;
            const sortOrder = sortOrderSelect.value;

            let filteredBooks = allBooks.filter(book => {
                // Asegúrate de que las propiedades existan antes de llamar a toLowerCase()
                const titleMatch = (book.title ? book.title.toLowerCase() : '').includes(searchTerm);
                const authorMatch = (book.author ? book.author.toLowerCase() : '').includes(searchTerm);
                return titleMatch || authorMatch;
            });

            filteredBooks.sort((a, b) => {
                let valA, valB;

                if (sortBy === 'created_at') {
                    // Convertir a objetos Date para una comparación precisa de tiempo
                    valA = new Date(a.created_at_timestamp);
                    valB = new Date(b.created_at_timestamp);
                } else if (sortBy === 'year') {
                    // Asegúrate de que 'year' sea un número, convierte a 0 si es nulo o inválido para evitar errores
                    valA = parseInt(a.year) || 0;
                    valB = parseInt(b.year) || 0;
                } else { // 'title' o 'author'
                    // Asegúrate de que las propiedades existan antes de llamar a toLowerCase()
                    valA = (a[sortBy] ? a[sortBy].toLowerCase() : '');
                    valB = (b[sortBy] ? b[sortBy].toLowerCase() : '');
                }
                
                if (valA < valB) {
                    return sortOrder === 'ASC' ? -1 : 1;
                }
                if (valA > valB) {
                    return sortOrder === 'ASC' ? 1 : -1;
                }
                return 0; // Son iguales
            });

            renderBooks(filteredBooks);
        }

    </script>
    
</body>
</html>
