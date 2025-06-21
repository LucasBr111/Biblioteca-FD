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
            background-color: #f8f9fa; /* Un fondo sutil para el área de usuario */
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1rem;
            color: #34495e;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            text-align: left;
            transition: background-color 0.2s ease;
        }
        .user-dropdown:hover {
            background-color: #e2e6ea;
            color: #34495e;
        }
        .user-dropdown .user-avatar {
            font-size: 1.5rem; /* Tamaño del icono de usuario */
            color: #6c757d;
        }
        .user-dropdown .fas.fa-chevron-down {
            margin-left: auto; /* Empuja la flecha a la derecha */
        }
        .dropdown-menu-offcanvas {
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
            color: #6c757d;
            white-space: normal; /* Permite que el texto se ajuste */
        }
        .dropdown-menu-offcanvas .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
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
                            <a class="nav-link dropdown-toggle user-dropdown d-flex align-items-center" href="#" id="userDropdownDesktop" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownDesktop">
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
                                    <li><a class="dropdown-item" href="index.php?c=account&a=login"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</a></li>
                                    <li><a class="dropdown-item" href="index.php?c=account&a=register"><i class="fas fa-user-plus me-2"></i>Registrarse</a></li>
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
                                <li><a class="dropdown-item" href="index.php?c=account&a=login" data-bs-dismiss="offcanvas"><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</a></li>
                                <li><a class="dropdown-item" href="index.php?c=account&a=register" data-bs-dismiss="offcanvas"><i class="fas fa-user-plus me-2"></i>Registrarse</a></li>
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

    <section id="libros" class="books-section py-5">
        <div class="container">
            <h2 class="text-center mb-5 animate_animated animate_fadeInDown">Nuestro Catálogo</h2>
            <div class="books-grid justify-content-center">
            <?php if (!empty($libros)):?>
    <?php foreach ($libros as $r): ?>
        <?php
            $displayPdfPath = htmlspecialchars($r['pdf_path']);
            if (strpos($displayPdfPath, 'assets/temp_uploads/') !== false) {
                $displayPdfPath = str_replace('assets/temp_uploads/', 'assets/books/', $displayPdfPath);
            }
        ?>
        <div class="book-card-wrapper"> <div class="card h-100 book-card animate_animated animate_fadeIn"
                data-id="<?= htmlspecialchars($r['id']) ?>"
                data-title="<?= htmlspecialchars($r['title']) ?>"
                data-author="<?= htmlspecialchars($r['author']) ?>"
                data-year="<?= htmlspecialchars($r['year']) ?>"
                data-genre="<?= htmlspecialchars($r['genre']) ?>"
                data-description="<?= htmlspecialchars($r['description']) ?>"
                data-image="<?= htmlspecialchars($r['image_path']) ?>"
                data-pdf="<?= $displayPdfPath ?>"
                data-summary="<?= htmlspecialchars($r['url_resumen']) ?>">
                <img src="<?= htmlspecialchars($r['image_path']) ?>" class="card-img-top book-image" alt="Portada de <?= htmlspecialchars($r['title']) ?>">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title book-title"><?= htmlspecialchars($r['title']) ?></h5>
                    <p class="card-text text-muted book-author">Por: <?= htmlspecialchars($r['author']) ?></p>
                    <p class="card-text book-description-short flex-grow-1"><?= htmlspecialchars(mb_strimwidth($r['description'], 0, 120, "...", "UTF-8")) ?></p>
            
                </div>
            </div>
        </div>
    
            <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="lead text-muted">No hay libros disponibles en el catálogo en este momento.</p>
                        <i class="fas fa-box-open fa-3x text-secondary mt-3"></i>
                    </div>
                <?php endif; ?>
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
        document.addEventListener('DOMContentLoaded', function() {
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
                            window.location.href = 'index.php?c=account&a=login';
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
    </script>
    
</body>
</html>