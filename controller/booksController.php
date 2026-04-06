<?php
// controller/booksController.php

if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'model/books.php';
require_once 'model/database.php';

class booksController
{
    private $bookModel;
    private $uploadDir       = 'assets/temp_uploads/';
    private $publishedPdfDir = 'assets/books/';
    private $defaultCover    = 'assets/img/default_cover.jpeg';

    public function __construct()
    {
        $this->bookModel = new books();
        $this->ensureDirs();
    }

    // ── Helpers ───────────────────────────────────────────────

    private function ensureDirs(): void
    {
        foreach ([$this->uploadDir, $this->publishedPdfDir, 'assets/covers/'] as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0775, true);
        }
    }

    private function requireLogin(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->alert('warning', 'Debés iniciar sesión para realizar esta acción.');
            $this->redirect('account', 'loginForm');
        }
    }

    private function requireAdmin(): void
    {
        $this->requireLogin();
        if (empty($_SESSION['is_admin'])) {
            $this->alert('error', 'Acceso denegado. Se requieren permisos de administrador.');
            $this->redirect('main');
        }
    }

    private function bookId(): int
    {
        $id = (int)($_POST['book_id'] ?? $_GET['id'] ?? 0);
        if ($id <= 0) throw new RuntimeException('ID de libro inválido.');
        return $id;
    }

    private function alert(string $type, string $title): void
    {
        $_SESSION['sweet_alert'] = ['type' => $type, 'title' => $title];
    }

    private function redirect(string $controller, string $action = 'index'): void
    {
        $url = "index.php?c={$controller}";
        if ($action !== 'index') $url .= "&a={$action}";
        header("Location: {$url}");
        exit();
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        $path = __DIR__ . '/../view/books/' . $view . '.php';
        if (!file_exists($path)) throw new RuntimeException("Vista '{$view}' no encontrada.");
        include $path;
    }

    private function jsonResponse(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    // ── Actions ───────────────────────────────────────────────

    /**
     * Mis Favoritos
     */
    public function favorites(): void
    {
        try {
            $this->requireLogin();
            $libros = $this->bookModel->getFavoritesBooks($_SESSION['user_id']);
            $this->render('favorites', ['libros' => $libros]);
        } catch (RuntimeException $e) {
            $this->alert('error', $e->getMessage());
            $this->redirect('main');
        }
    }

    /**
     * Subida de libro (pull request)
     */
    public function upload(): void
    {
        try {
            $this->requireLogin();

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new RuntimeException('Método no permitido.');
            }

            // Validaciones básicas
            $title  = trim($_POST['title'] ?? '');
            $author = trim($_POST['author'] ?? '');
            if (!$title || !$author) throw new RuntimeException('El título y el autor son obligatorios.');

            // Subida PDF
            if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
                throw new RuntimeException('El archivo PDF es obligatorio.');
            }
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $pdfMime  = finfo_file($finfo, $_FILES['pdf_file']['tmp_name']);
            if ($pdfMime !== 'application/pdf') throw new RuntimeException('Solo se aceptan archivos PDF.');
            if ($_FILES['pdf_file']['size'] > 50 * 1024 * 1024) throw new RuntimeException('El PDF no puede superar 50 MB.');

            $pdfName  = uniqid('pdf_') . '.pdf';
            $pdfPath  = $this->uploadDir . $pdfName;
            if (!move_uploaded_file($_FILES['pdf_file']['tmp_name'], $pdfPath)) {
                throw new RuntimeException('Error al guardar el PDF.');
            }

            // Subida imagen
            $imgPath = $this->defaultCover;
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $imgMime = finfo_file($finfo, $_FILES['cover_image']['tmp_name']);
                finfo_close($finfo);
                if (!in_array($imgMime, ['image/jpeg','image/png','image/gif','image/webp'])) {
                    throw new RuntimeException('La portada debe ser una imagen válida (JPG, PNG, GIF, WEBP).');
                }
                if ($_FILES['cover_image']['size'] > 5 * 1024 * 1024) {
                    throw new RuntimeException('La imagen no puede superar 5 MB.');
                }
                $ext     = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
                $imgName = uniqid('cover_') . '.' . $ext;
                $imgPath = $this->uploadDir . $imgName;
                if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $imgPath)) {
                    throw new RuntimeException('Error al guardar la imagen de portada.');
                }
            } else {
                finfo_close($finfo);
            }

            $bookId = $this->bookModel->uploadBookPull([
                'title'           => $title,
                'author'          => $author,
                'year'            => !empty($_POST['year']) ? (int)$_POST['year'] : (int)date('Y'),
                'genre'           => trim($_POST['genre'] ?? ''),
                'description'     => trim($_POST['description'] ?? ''),
                'cover_image_path'=> $imgPath,
                'pdf_path'        => $pdfPath,
                'url_resumen'     => trim($_POST['summary_url'] ?? '') ?: null,
                'uploaded_by'     => $_SESSION['user_id']
            ]);

            if (!$bookId) throw new RuntimeException('Error al registrar el libro en la base de datos.');

            $this->alert('success', '¡Libro enviado! Un administrador lo revisará pronto.');
            $this->redirect('main');

        } catch (RuntimeException $e) {
            $this->alert('error', $e->getMessage());
            $this->redirect('main');
        }
    }

    /**
     * Panel de peticiones pendientes (admin)
     */
    public function managePulls(): void
    {
        try {
            $this->requireAdmin();
            $pulls = $this->bookModel->getAllPulls();
            $this->render('manage_pulls', ['pulls' => $pulls]);
        } catch (RuntimeException $e) {
            $this->alert('error', $e->getMessage());
            $this->redirect('main');
        }
    }

    /**
     * Aceptar un pull y publicar el libro
     */
    public function acceptPull(): void
    {
        try {
            $this->requireAdmin();

            $id   = $this->bookId();
            $book = $this->bookModel->getBookById($id);
            if (!$book || $book['publicado'] === 'si') throw new RuntimeException('Solicitud no encontrada o ya publicada.');

            // Mover PDF a directorio final
            if (!empty($book['pdf_path']) && file_exists($book['pdf_path'])) {
                $newPdf = $this->publishedPdfDir . basename($book['pdf_path']);
                rename($book['pdf_path'], $newPdf);
            }

            // Mover portada a directorio final (si no es la por defecto)
            if (!empty($book['image_path']) && $book['image_path'] !== $this->defaultCover && file_exists($book['image_path'])) {
                $newImg = 'assets/temp_uploads/' . basename($book['image_path']);
                rename($book['image_path'], $newImg);
            }

            $this->bookModel->publishBook($id);
            $this->alert('success', "El libro «{$book['title']}» fue publicado correctamente.");
            $this->redirect('books', 'managePulls');

        } catch (RuntimeException $e) {
            $this->alert('error', $e->getMessage());
            $this->redirect('books', 'managePulls');
        }
    }

    /**
     * Rechazar un pull y eliminar archivos temporales
     */
    public function denyPull(): void
    {
        try {
            $this->requireAdmin();

            $id   = $this->bookId();
            $book = $this->bookModel->getBookById($id);
            if (!$book) throw new RuntimeException('Solicitud no encontrada.');

            $this->deleteFiles($book);
            $this->bookModel->deleteBook($id);

            $this->alert('success', "La solicitud de «{$book['title']}» fue rechazada y eliminada.");
            $this->redirect('books', 'managePulls');

        } catch (RuntimeException $e) {
            $this->alert('error', $e->getMessage());
            $this->redirect('books', 'managePulls');
        }
    }

    /**
     * Procesar actualización de libro (admin)
     */
    public function edit(): void
    {
        try {
            $this->requireAdmin();
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new RuntimeException('Método no permitido.');
            
            $id = $this->bookId();
            $data = [
                'title'       => trim($_POST['title'] ?? ''),
                'author'      => trim($_POST['author'] ?? ''),
                'year'        => !empty($_POST['year']) ? (int)$_POST['year'] : null,
                'genre'       => trim($_POST['genre'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];
            
            if (!$data['title'] || !$data['author']) throw new RuntimeException('Título y autor son obligatorios.');
            
            if ($this->bookModel->updateBook($id, $data)) {
                $this->alert('success', "Libro actualizado correctamente.");
            } else {
                throw new RuntimeException("Error al actualizar el libro.");
            }
            
            // Redirect back to referring page or admin dashboard
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?c=account&a=adminDashboard';
            header("Location: $referer");
            exit();

        } catch (RuntimeException $e) {
            $this->alert('error', $e->getMessage());
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?c=account&a=adminDashboard';
            header("Location: $referer");
            exit();
        }
    }

    /**
     * Eliminar libro publicado (admin)
     */
    public function delete(): void
    {
        try {
            $this->requireAdmin();
            $id   = $this->bookId();
            $book = $this->bookModel->getBookById($id);
            if (!$book) throw new RuntimeException('Libro no encontrado.');

            $this->deleteFiles($book);
            $this->bookModel->deleteBook($id);

            $this->alert('success', "El libro «{$book['title']}» fue eliminado del catálogo.");
            
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?c=main';
            header("Location: $referer");
            exit();

        } catch (RuntimeException $e) {
            $this->alert('error', $e->getMessage());
            $referer = $_SERVER['HTTP_REFERER'] ?? 'index.php?c=main';
            header("Location: $referer");
            exit();
        }
    }

    /**
     * Toggle like/unlike — responde con JSON
     */
    public function toggleLike(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['error' => 'No autenticado'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Método no permitido'], 405);
        }

        $bookId = (int)($_POST['book_id'] ?? 0);
        if ($bookId <= 0) $this->jsonResponse(['error' => 'ID inválido'], 400);

        $userId = (int)$_SESSION['user_id'];
        $result = $this->bookModel->toggleLike($userId, $bookId);

        $this->jsonResponse($result);
    }

    // ── Private file helpers ──────────────────────────────────

    private function deleteFiles(array $book): void
    {
        foreach (['pdf_path', 'image_path'] as $field) {
            if (!empty($book[$field]) && $book[$field] !== $this->defaultCover && file_exists($book[$field])) {
                @unlink($book[$field]);
            }
        }
    }
}