<?php
/**
 * Week9/admin.php - Front Controller Wrapper untuk Dashboard Admin & CRUD Cepatik (Laravel 9)
 * Memungkinkan akses langsung melalui http://localhost:8000/Week9/admin.php
 */
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
$_SERVER['REQUEST_URI'] = '/admin.php' . $query;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__ . '/public/index.php';
