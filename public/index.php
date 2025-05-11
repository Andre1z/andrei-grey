<?php
// public/index.php

// Incluir el autoloader de Composer y la configuración global
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';

use App\Controllers\FileController;
use App\Helpers\Translation;

// Detectar el idioma. Se puede establecer mediante el parámetro GET "lang", o se usa 'en' por defecto.
$language = isset($_GET['lang']) ? $_GET['lang'] : 'en';

// Instanciar el gestor de traducciones usando el CSV ubicado en la carpeta 'translations'
$translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);

// Obtener la ruta de la petición para un enrutamiento muy básico.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Si la ruta es "/upload", se invoca el controlador encargado de manejar la carga y transformación del ODS.
if ($uri === '/upload') {
    $controller = new FileController();
    $controller->upload();
    exit; // Finaliza la ejecución después de procesar la solicitud de carga.
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($translator->get('login_title')); ?></title>
    <!-- Aquí puedes enlazar tu CSS, por ejemplo: -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($translator->get('login_title')); ?></h1>
    </header>

    <main>
        <!-- Un formulario de ejemplo para subir el archivo ODS a transformar -->
        <form action="/upload" method="post" enctype="multipart/form-data">
            <div>
                <label for="ods_file"><?php echo htmlspecialchars($translator->get('enter_ods_url')); ?></label>
                <!-- Nota: Puedes ajustar la etiqueta según convenga, por ejemplo usar un input file o una URL -->
                <input type="file" name="ods_file" id="ods_file" required>
            </div>
            <div>
                <button type="submit"><?php echo htmlspecialchars($translator->get('login_button')); ?></button>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> - <?php echo htmlspecialchars($translator->get('dashboard_title')); ?></p>
    </footer>
    
    <!-- Opcional: enlazar archivos JavaScript -->
    <script src="js/main.js"></script>
</body>
</html>