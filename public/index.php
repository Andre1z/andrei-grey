<?php
// public/index.php

// Cargar el autoloader de Composer y la configuración global
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';

use App\Controllers\FileController;
use App\Helpers\Translation;

// Gestión del idioma (se puede establecer con ?lang=es en la URL)
$language   = isset($_GET['lang']) ? $_GET['lang'] : 'en';
$translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);

// Si se envía el formulario (método POST) procesamos la subida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ods_file'])) {
    $fileController = new FileController();
    // Se asume que upload() ha sido modificado para retornar el nombre del archivo subido
    $uploadedFile = $fileController->upload();
    if ($uploadedFile) {
        // Redirige a index.php pasando el nombre del archivo para la previsualización
        header("Location: " . BASE_URL . "/index.php?file=" . urlencode($uploadedFile));
        exit;
    }
}

// Obtenemos la variable opcional "file" para saber si hay un archivo a previsualizar
$previewFile = isset($_GET['file']) ? $_GET['file'] : '';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($translator->get('Inicio')); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($translator->get('Inicio')); ?></h1>
    </header>
    <main>
        <!-- Formulario para subir el archivo ODS -->
        <form action="<?php echo BASE_URL; ?>/index.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="ods_file"><?php echo htmlspecialchars($translator->get('Seleccione un archivo .ods')); ?></label>
                <input type="file" name="ods_file" id="ods_file" required>
            </div>
            <div>
                <button type="submit"><?php echo htmlspecialchars($translator->get('Submit')); ?></button>
            </div>
        </form>

        <!-- Si se subió un archivo, mostramos su previsualización en un iframe -->
        <?php if (!empty($previewFile)): ?>
            <h2>Vista previa del archivo: <?php echo htmlspecialchars($previewFile); ?></h2>
            <iframe style="width:100%; height:500px;" src="<?php echo BASE_URL; ?>/view/preview.php?file=<?php echo urlencode($previewFile); ?>"></iframe>
        <?php endif; ?>

        <!-- Sección para listar todos los archivos subidos -->
        <section>
            <h2>Archivos subidos</h2>
            <ul>
                <?php
                // Obtiene la ruta física a la carpeta de uploads
                $uploadsDir = __DIR__ . '/uploads/';
                if (is_dir($uploadsDir)) {
                    $files = array_diff(scandir($uploadsDir), array('..', '.'));
                    foreach ($files as $file) {
                        // El enlace redirige a index.php con ?file= para mostrar su preview
                        echo '<li><a href="' . BASE_URL . '/index.php?file=' . urlencode($file) . '">' . htmlspecialchars($file) . '</a></li>';
                    }
                } else {
                    echo '<li>No hay archivos subidos.</li>';
                }
                ?>
            </ul>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date('Y'); ?> - <?php echo htmlspecialchars($translator->get('dashboard_title')); ?></p>
    </footer>
    <script src="<?php echo BASE_URL; ?>/js/main.js"></script>
</body>
</html>