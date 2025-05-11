<?php
session_start();

// Limpiar todas las variables de sesión
$_SESSION = array();

// Si se usa cookies para la sesión, eliminarlas
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Cargar la configuración para usar BASE_URL
require_once __DIR__ . '/../app/config.php';

// Redirigir al login
header("Location: " . BASE_URL . "/login.php");
exit;
?>