<?php

// Centralizar el inicio de sesión al inicio de la aplicación
// Si esto ya se hace en un archivo de bootstrap o de enrutamiento principal, puedes eliminarlo de aquí.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'model/books.php';
require_once 'model/database.php'; // Asegúrate de que esto cargue tu clase Database y PDO

class booksController
{
    private $bookModel;
    private $uploadDir = 'assets/temp_uploads/'; // Directorio para archivos subidos temporalmente por usuarios
    private $publishedPdfDir = 'assets/books/'; // Directorio final para PDFs publicados
    private $publishedCoversDir = 'assets/covers/'; // Directorio final para portadas publicadas
    private $defaultCover = 'assets/default_cover.jpg'; // Ruta a la imagen de portada por defecto

    public function __construct()
    {
        $this->bookModel = new books(); // Instancia del modelo Books
        $this->ensureDirectoriesExist(); // Asegura que los directorios necesarios existan
        // $this->ensureSession(); // ELIMINADO: session_start() debe ser llamada una sola vez al inicio de la aplicación.
                                  // Ya lo moví al principio del archivo para este ejemplo, o debe estar en tu index.php principal.
    }

    /**
     * Crea directorios si no existen
     */
    private function ensureDirectoriesExist()
    {
        $dirs = [$this->uploadDir, $this->publishedPdfDir, $this->publishedCoversDir];
        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                // Usar 0775 es una buena práctica para que el grupo web también pueda escribir.
                // Ajusta los permisos según la configuración de tu servidor.
                if (!mkdir($dir, 0775, true)) {
                    throw new Exception("No se pudo crear el directorio: {$dir}. Verifique permisos.");
                }
            }
        }
    }

    /**
     * ELIMINADA: La llamada a session_start() se ha movido fuera de la clase
     * para asegurar que se ejecute una sola vez al inicio de la petición.
     * Si tu framework/aplicación ya la maneja globalmente, puedes eliminar esta función y su llamada en el constructor.
     */
    // private function ensureSession()
    // {
    //     if (session_status() === PHP_SESSION_NONE) {
    //         session_start();
    //     }
    // }

    /**
     * Valida si el usuario está logueado
     */
    private function validateLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception("Debes iniciar sesión para realizar esta acción.");
        }
    }

    /**
     * Valida si el usuario es administrador
     */
    private function validateAdmin()
    {
        $this->validateLogin(); // Primero verifica si está logueado
        if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
            throw new Exception("Acceso denegado. Permisos de administrador requeridos.");
        }
    }

    /**
     * Valida token CSRF
     */
    private function validateCSRF()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception("Token de seguridad inválido. Recarga la página e intenta de nuevo.");
        }
    }

    /**
     * Valida datos del formulario de subida
     */
    private function validateUploadData()
    {
        $errors = [];

        if (empty($_POST['title'])) {
            $errors[] = "El título es obligatorio.";
        }

        if (empty($_POST['author'])) {
            $errors[] = "El autor es obligatorio.";
        }

        // --- Validación del archivo PDF ---
        if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "El archivo PDF es obligatorio.";
        } else {
            // Validar tipo de archivo PDF
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['pdf_file']['tmp_name']);
            finfo_close($finfo);
            
            if ($mimeType !== 'application/pdf') {
                $errors[] = "El archivo debe ser un PDF válido.";
            }

            // Validar tamaño (ej: máximo 50MB)
            if ($_FILES['pdf_file']['size'] > 50 * 1024 * 1024) {
                $errors[] = "El archivo PDF no puede superar los 50MB.";
            }
        }

        // --- Validación de la imagen de portada (opcional) ---
        // Nombre del campo de archivo en el formulario debe ser 'cover_image' (por consistencia)
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['cover_image']['tmp_name']); // CAMBIO: Usar 'cover_image'
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = "La portada debe ser una imagen válida (JPG, PNG o GIF).";
            }

            if ($_FILES['cover_image']['size'] > 5 * 1024 * 1024) { // CAMBIO: Usar 'cover_image'
                $errors[] = "La imagen de portada no puede superar los 5MB.";
            }
        }

        if (!empty($errors)) {
            throw new Exception(implode(" ", $errors));
        }
    }

    /**
     * Procesa la subida de archivos y los mueve al directorio temporal.
     * Retorna un array con las rutas relativas de los archivos movidos.
     */
    private function processFileUploads()
    {
        $uploads = [];

        // Procesar PDF
        $pdfExtension = pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION);
        $pdfFileName = uniqid('pdf_') . '.' . $pdfExtension;
        $pdfPath = $this->uploadDir . $pdfFileName; // Ruta temporal en assets/temp_uploads/

        if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfPath)) {
            throw new Exception("Error al subir el archivo PDF.");
        }
        $uploads['pdf_path'] = $pdfPath;

        // Procesar imagen de portada
        // CAMBIO CLAVE: Consistencia en el nombre 'cover_image' en $_FILES
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $imageExtension = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
            $imageFileName = uniqid('cover_') . '.' . $imageExtension;
            $imagePath = $this->uploadDir . $imageFileName; // Ruta temporal en assets/temp_uploads/

            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $imagePath)) {
                throw new Exception("Error al subir la imagen de portada.");
            }
            // CAMBIO CLAVE: El nombre de la clave en $uploads debe ser 'cover_image_path'
            // para coincidir con la base de datos y el modelo.
            $uploads['cover_image_path'] = $imagePath;
        } else {
            // Si no se sube imagen, usa la por defecto
            // CAMBIO CLAVE: Usar 'cover_image_path' para la clave
            $uploads['cover_image_path'] = $this->defaultCover;
        }

        return $uploads;
    }

    /**
     * Sube un nuevo libro (pull request)
     */
    public function upload()
    {
        try {
            $this->validateLogin();
            $this->validateCSRF();
            $this->validateUploadData(); // Valida que los archivos estén presentes y correctos

            $uploads = $this->processFileUploads(); // Mueve los archivos a assets/temp_uploads/


            $data = [
                'title' => trim($_POST['title']),
                'author' => trim($_POST['author']),
                'year' => !empty($_POST['year']) ? (int)$_POST['year'] : date('Y'),
                'genre' => trim($_POST['genre'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'cover_image_path' => $uploads['cover_image_path'], 
                'pdf_path' => $uploads['pdf_path'],
                'url_resumen' => trim($_POST['summary_url'] ?? '') ?: null,
                'user_id' => $_SESSION['user_id']
            ];

            error_log("Intentando subir libro: " . print_r($data, true)); // Para depuración

            $bookId = $this->bookModel->uploadBookPull($data);
            
            if ($bookId) {
                $this->showAlert('success', 'Libro subido exitosamente', 
                    'Tu libro ha sido enviado para revisión. Un administrador lo aprobará pronto.');
                $this->redirect('main');
            } else {
                // Si falla la inserción en BD, intenta eliminar los archivos temporales que se subieron.
                $this->deleteFilesOnError($uploads); // Nueva función para limpieza
                throw new Exception("Error al guardar el libro en la base de datos.");
            }

        } catch (Exception $e) {
            error_log("Error en upload: " . $e->getMessage());
            // Si hay un error, SweetAlert lo mostrará
            $this->showAlert('error', 'Error al subir el libro', $e->getMessage());
            // No necesitas un redirect en el catch si ya se hace en la función de error.
            $this->redirect('main');
        }
    }

    /**
     * Muestra la página de gestión de pulls (solo admin)
     */
    public function managePulls()
    {
        try {
            $this->validateAdmin();
            
            // Obtener pulls con su estado 'publicado' = 'no'
            $pulls = $this->bookModel->getAllPulls(); 
            $this->render('manage_pulls', ['pulls' => $pulls]);
            
        } catch (Exception $e) {
            $this->showAlert('error', 'Acceso denegado', $e->getMessage());
            $this->redirect('main');
        }
    }

    /**
     * Acepta un pull request
     */
    public function acceptPull()
    {
        try {
            $this->validateAdmin();
            
            $bookId = $this->getBookIdFromRequest(); // Obtiene el ID del libro
            $book = $this->getBookForPull($bookId); // Obtiene todos los datos del libro (incluidas rutas temporales)
            
            // Mover los archivos de temp_uploads a sus directorios finales de publicación
            $this->moveFilesToPublished($book); 
            
            // Actualizar el estado del libro a "publicado" y sus rutas en la BD
            $this->bookModel->publishBook($bookId, $book['pdf_path'], $book['cover_image_path']); 
            
            $this->showAlert('success', 'Libro publicado', 
                "El libro '{$book['title']}' ha sido publicado exitosamente.");
            $this->redirect('books', 'managePulls');
            
        } catch (Exception $e) {
            error_log("Error en acceptPull: " . $e->getMessage());
            $this->showAlert('error', 'Error al publicar', $e->getMessage());
            $this->redirect('books', 'managePulls');
        }
    }

    /**
     * Deniega un pull request
     */
    public function denyPull()
    {
        try {
            $this->validateAdmin();
            
            $bookId = $this->getBookIdFromRequest();
            $book = $this->getBookForPull($bookId); // Obtiene los datos del libro para poder borrar los archivos
            
            $this->deleteTemporaryFiles($book); // Elimina los archivos de temp_uploads
            $this->bookModel->deleteBook($bookId); // Elimina el registro de la BD
            
            $this->showAlert('success', 'Solicitud denegada', 
                "La solicitud del libro '{$book['title']}' ha sido eliminada.");
            $this->redirect('books', 'managePulls');
            
        } catch (Exception $e) {
            error_log("Error en denyPull: " . $e->getMessage());
            $this->showAlert('error', 'Error al denegar', $e->getMessage());
            $this->redirect('books', 'managePulls');
        }
    }

    /**
     * Obtiene y valida el ID del libro desde la request (GET o POST)
     */
    private function getBookIdFromRequest()
    {
        // Prioriza POST para acciones de formulario, luego GET para enlaces directos
        $bookId = (int)($_POST['book_id'] ?? $_GET['id'] ?? 0); 
        if ($bookId <= 0) {
            throw new Exception("ID de libro inválido.");
        }
        return $bookId;
    }

    /**
     * Obtiene un libro de la base de datos para operaciones de pull
     * Verifica que el libro exista y no esté ya publicado.
     */
    private function getBookForPull($bookId)
    {
        $book = $this->bookModel->getBookById($bookId); // Asume que getBookById devuelve un array asociativo

        if (!$book) {
            throw new Exception("Solicitud no encontrada.");
        }
        
        // Asume que la columna 'publicado' es un string ('si'/'no')
        if ($book['publicado'] === 'si') { 
            throw new Exception("Este libro ya está publicado.");
        }
        
        return $book;
    }

    /**
     * Mueve archivos desde el directorio temporal al directorio publicado.
     * Actualiza las rutas en el objeto $book que se le pasa por referencia
     * para que contengan las nuevas rutas publicadas.
     *
     * @param array $book Array asociativo con los datos del libro, incluyendo 'pdf_path' y 'cover_image_path'.
     */
    private function moveFilesToPublished(&$book) // Recibe $book por referencia para actualizar sus rutas
    {
        // Mover PDF
        if (!empty($book['pdf_path']) && $book['pdf_path'] !== 'assets/temp_uploads/') { // Asegura que no esté vacío o solo el directorio
            $oldPdfPath = $book['pdf_path'];
            $fileName = basename($oldPdfPath);
            $newPdfPath = $this->publishedPdfDir . $fileName;

            if (file_exists($oldPdfPath) && rename($oldPdfPath, $newPdfPath)) {
                $book['pdf_path'] = $newPdfPath; // Actualiza la ruta en el objeto $book
            } else {
                error_log("Error al mover PDF de {$oldPdfPath} a {$newPdfPath}. Existe: " . (file_exists($oldPdfPath) ? 'Sí' : 'No'));
                throw new Exception("El archivo PDF no se encuentra o no se puede mover a la ubicación final.");
            }
        } else {
            // Si no hay PDF válido en temp_uploads, lanzar un error
            throw new Exception("La ruta del archivo PDF temporal es inválida o no existe.");
        }

        // Mover imagen de portada (solo si no es la por defecto)
        // CAMBIO CLAVE: Ahora se espera 'cover_image_path' del array $book
        if (!empty($book['cover_image_path']) && $book['cover_image_path'] !== $this->defaultCover) {
            $oldCoverPath = $book['cover_image_path'];
            $fileName = basename($oldCoverPath);
            $newCoverPath = $this->publishedCoversDir . $fileName;

            if (file_exists($oldCoverPath) && rename($oldCoverPath, $newCoverPath)) {
                $book['cover_image_path'] = $newCoverPath; // Actualiza la ruta en el objeto $book
            } else {
                 error_log("Error al mover portada de {$oldCoverPath} a {$newCoverPath}. Existe: " . (file_exists($oldCoverPath) ? 'Sí' : 'No'));
                throw new Exception("El archivo de portada no se encuentra o no se puede mover a la ubicación final.");
            }
        }
        // Si es la portada por defecto, no hay nada que mover ni actualizar.
    }

    /**
     * Elimina un archivo individual.
     * Se usa para limpieza de errores o denegaciones.
     *
     * @param string $filePath La ruta completa del archivo a eliminar.
     * @param string $fileType El tipo de archivo (para mensajes de error).
     * @return bool True si se eliminó, false en caso contrario.
     */
    private function deleteFile(string $filePath, string $fileType): bool
    {
        if (file_exists($filePath)) {
            if (unlink($filePath)) {
                return true;
            } else {
                error_log("Error al eliminar {$fileType}: {$filePath}. Permiso denegado.");
                return false;
            }
        }
        // No es un error si el archivo ya no existe (ej: ya se movió o eliminó previamente)
        return true; 
    }

    /**
     * Elimina archivos temporales asociados a un libro (usado en denyPull o si falla upload).
     */
    private function deleteTemporaryFiles($book)
    {
        // Asegúrate de que las claves existen y que las rutas no sean la default_cover si es que la portada es la misma que la default_cover
        if (!empty($book['pdf_path']) && file_exists($book['pdf_path'])) {
            $this->deleteFile($book['pdf_path'], 'PDF temporal');
        }
        
        // Solo elimina la imagen si no es la por defecto y si existe
        if (!empty($book['cover_image_path']) && 
            $book['cover_image_path'] !== $this->defaultCover && 
            file_exists($book['cover_image_path'])) {
            $this->deleteFile($book['cover_image_path'], 'portada temporal');
        }
    }

    /**
     * Elimina los archivos subidos si falla la inserción en la base de datos durante upload().
     * @param array $uploads Array con las rutas de los archivos subidos ($uploads['pdf_path'], $uploads['cover_image_path'])
     */
    private function deleteFilesOnError(array $uploads)
    {
        if (isset($uploads['pdf_path']) && file_exists($uploads['pdf_path'])) {
            unlink($uploads['pdf_path']);
        }
        // Solo elimina la imagen si no es la por defecto
        if (isset($uploads['cover_image_path']) && 
            $uploads['cover_image_path'] !== $this->defaultCover && 
            file_exists($uploads['cover_image_path'])) {
            unlink($uploads['cover_image_path']);
        }
    }

    /**
     * Genera token CSRF si no existe
     */
    private function generateCSRFToken()
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Renderiza una vista
     */
    private function render(string $viewName, array $data = [])
    {
        extract($data); // Extrae las variables del array $data
        $csrf_token = $this->generateCSRFToken(); // Genera token CSRF para el formulario

        $viewPath = __DIR__ . '/../view/books/' . $viewName . '.php'; // Ruta completa a la vista
        
        if (!file_exists($viewPath)) {
            throw new Exception("Vista '{$viewName}.php' no encontrada en '{$viewPath}'");
        }
        
        include $viewPath; // Incluye el archivo de la vista
    }

    /**
     * Muestra alerta con SweetAlert (guarda en sesión para mostrar en el siguiente request)
     */
    private function showAlert(string $type, string $title, string $text = '')
    {
        $_SESSION['sweet_alert'] = [
            'type' => $type,
            'title' => $title,
            'text' => $text
        ];
    }

    /**
     * Redirige a una página
     */
    private function redirect(string $controller, string $action = 'index')
    {
        $location = "index.php?c={$controller}";
        if ($action !== 'index') {
            $location .= "&a={$action}";
        }
        header("Location: {$location}");
        exit();
    }
}