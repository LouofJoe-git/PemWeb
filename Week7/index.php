<?php
/**
 * index.php - Cepatik Entry Point / Front Controller
 * 
 * Mengimplementasikan arsitektur MVC (Model-View-Controller) untuk Cepatik.
 * Mengarahkan request pengguna ke Controller yang sesuai:
 * - default / 'home' -> HomeController
 * - 'cetak'          -> CetakController
 * - 'admin'          -> AdminController
 */

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'cetak':
        require_once __DIR__ . '/app/Controllers/CetakController.php';
        $controller = new CetakController();
        $controller->index();
        break;

    case 'admin':
        require_once __DIR__ . '/app/Controllers/AdminController.php';
        $controller = new AdminController();
        $controller->index();
        break;

    case 'home':
    default:
        require_once __DIR__ . '/app/Controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
