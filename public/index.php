<?php
session_start();

// Si el usuario no está logueado, redirige a login.php
if (!isset($_SESSION['username'])) {
    header("Location: " . BASE_URL . "/login.php");
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';

use App\Controllers\FileController;
use App\Helpers\Translation;

// Se toma el idioma desde la sesión; si no existe, se asigna "en" por defecto
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Instanciar la clase de traducciones usando el archivo translations.csv
$translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);

// Procesar la subida del archivo si se envía el formulario vía POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['ods_file'])) {
    $fileController = new FileController();
    // Se asume que el método upload() retorna el nombre del archivo subido en caso de éxito
    $uploadedFile = $fileController->upload();
    if ($uploadedFile) {
        header("Location: " . BASE_URL . "/index.php?file=" . urlencode($uploadedFile));
        exit;
    }
}

// Si se pasa el parámetro "file" en la URL, se usará para mostrar la previsualización
$previewFile = isset($_GET['file']) ? $_GET['file'] : '';

?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($translator->get('dashboard_title')); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($translator->get('dashboard_title')); ?></h1>
        <p><?php echo htmlspecialchars($translator->get('hello')); ?>, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <p><a href="<?php echo BASE_URL; ?>/logout.php"><?php echo htmlspecialchars($translator->get('logout')); ?></a></p>
    </header>
    <main>
        <!-- Sección de importación (formulario para subir el archivo ODS) -->
        <section>
            <h2><?php echo htmlspecialchars($translator->get('importer_title')); ?></h2>
            <form action="<?php echo BASE_URL; ?>/index.php" method="post" enctype="multipart/form-data">
                <div>
                    <label for="ods_file"><?php echo htmlspecialchars($translator->get('enter_ods_url')); ?></label>
                    <input type="file" name="ods_file" id="ods_file" required>
                </div>
                <div>
                    <button type="submit"><?php echo htmlspecialchars($translator->get('Submit')); ?></button>
                </div>
            </form>
        </section>
        
        <!-- Si se ha subido un archivo, se muestra la previsualización en un iframe -->
        <?php if (!empty($previewFile)): ?>
        <section>
            <h2><?php echo htmlspecialchars($translator->get('importer_heading')); ?></h2>
            <iframe style="width:100%; height:500px;" src="<?php echo BASE_URL; ?>/view/preview.php?file=<?php echo urlencode($previewFile); ?>"></iframe>
        </section>
        <?php endif; ?>
        
        <!-- Sección para listar los archivos subidos -->
        <section>
            <h2><?php echo htmlspecialchars($translator->get('tables')); ?></h2>
            <ul>
                <?php
                // Ruta física a la carpeta de uploads
                $uploadsDir = __DIR__ . '/uploads/';
                if (is_dir($uploadsDir)) {
                    $files = array_diff(scandir($uploadsDir), array('..', '.'));
                    foreach ($files as $file) {
                        echo '<li><a href="' . BASE_URL . '/index.php?file=' . urlencode($file) . '">' . htmlspecialchars($file) . '</a></li>';
                    }
                } else {
                    echo '<li>' . htmlspecialchars("No hay archivos subidos.") . '</li>';
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