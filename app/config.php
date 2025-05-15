<?php
/**
 * Configuración global para el proyecto andrei | grey.
 *
 * Este archivo define constantes y configuraciones básicas utilizadas en toda la aplicación.
 */

// Mostrar todos los errores (en entorno de desarrollo, en producción desactivar display_errors)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer la zona horaria
date_default_timezone_set('Europe/Madrid');

// Define la URL base de la aplicación, si no está ya definida
if (!defined('BASE_URL')) {
    define('BASE_URL', '/andrei-grey/andrei-grey/public');
}

// Idioma predeterminado
if (!defined('DEFAULT_LANGUAGE')) {
    define('DEFAULT_LANGUAGE', 'en');
}

// Otras constantes de configuración pueden agregarse a continuación...
// Ejemplo: Definir rutas absolutas a directorios importantes
if (!defined('APP_PATH')) {
    define('APP_PATH', realpath(__DIR__));
}
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', realpath(APP_PATH . '/../'));
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', ROOT_PATH . '/storage');
}
if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', ROOT_PATH . '/public/uploads');
}
if (!defined('TRANSLATIONS_PATH')) {
    define('TRANSLATIONS_PATH', ROOT_PATH . '/translations');
}

// Configuración extra (por ejemplo, para conexión a base de datos si fuera necesario)
// if (!defined('DB_HOST')) {
//     define('DB_HOST', 'localhost');
// }
// if (!defined('DB_NAME')) {
//     define('DB_NAME', 'mi_base_de_datos');
// }
// if (!defined('DB_USER')) {
//     define('DB_USER', 'root');
// }
// if (!defined('DB_PASS')) {
//     define('DB_PASS', '');
// }