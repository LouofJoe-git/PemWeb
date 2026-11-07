<?php
/**
 * BaseController.php - Cepatik MVC Controller
 * 
 * Controller dasar yang menyediakan method render view,
 * penanganan input HTTP (GET/POST), sanitasi data,
 * serta respon JSON untuk kebutuhan aplikasi Cepatik.
 */

class BaseController
{
    /**
     * Me-render template View dan mengekstrak data ke dalam variabel lokal
     *
     * @param string $viewPath Path relatif terhadap folder app/Views (cth: 'home/index')
     * @param array  $data     Data asosiatif yang akan diteruskan ke View
     */
    protected function render(string $viewPath, array $data = []): void
    {
        // Ekstrak array asosiatif menjadi variabel PHP lokal di dalam View
        extract($data);

        $viewFile = __DIR__ . '/../Views/' . $viewPath . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo "<h1>Error 404: View '$viewPath' tidak ditemukan.</h1>";
            return;
        }

        require $viewFile;
    }

    /**
     * Memeriksa apakah request bertipe POST
     */
    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Memeriksa apakah request bertipe GET
     */
    protected function isGet(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
    }

    /**
     * Mengambil dan membersihkan nilai parameter dari $_POST
     */
    protected function getPost(string $key, $default = null)
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        return is_string($_POST[$key]) ? trim($_POST[$key]) : $_POST[$key];
    }

    /**
     * Mengambil dan membersihkan nilai parameter dari $_GET
     */
    protected function getQuery(string $key, $default = null)
    {
        if (!isset($_GET[$key])) {
            return $default;
        }
        return is_string($_GET[$key]) ? trim($_GET[$key]) : $_GET[$key];
    }

    /**
     * Sanitasi string untuk mencegah XSS
     */
    protected function sanitize(string $val): string
    {
        return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Mengirim respons JSON
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
