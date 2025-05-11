<?php
// app/config.php

/**
 * Configuración Global de la Aplicación
 *
 * Este archivo se encarga de establecer las configuraciones generales,
 * como las rutas base, los directorios y parámetros de entorno (por ejemplo, la zona horaria).
 */

// Mostrar todos los errores (modo desarrollo). En producción, debe deshabilitarse.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir el directorio base del proyecto (nivel raíz)
define('BASE_PATH', dirname(__DIR__));

// Definir la ruta para la base de datos SQLite.
// Si la constante DB_PATH ya se definió en otro lugar, se mantiene; de lo contrario se define aquí.
if (!defined('DB_PATH')) {
    define('DB_PATH', BASE_PATH . '/storage/database.sqlite');
}

// Definir la ruta para la carpeta de uploads (para archivos ODS subidos)
if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', BASE_PATH . '/public/uploads/');
}

// Configurar la zona horaria (ejemplo: Europa/Madrid para el territorio español)
date_default_timezone_set('Europe/Madrid');

// Aquí puedes definir otras configuraciones globales, tales como parámetros para sesiones,
// configuración de otros servicios o claves de API, etc.

// Ejemplo adicional: Configuración de una constante para la versión de la aplicación
define('APP_VERSION', '1.0.0');