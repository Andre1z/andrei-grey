<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';

use App\Controllers\FileController;
use App\Controllers\PreviewController;
use App\Helpers\Translation;

$language = isset($_GET['lang']) ? $_GET['lang'] : 'en';
$translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($uri === BASE_URL . '/preview' && isset($_GET['file'])) {
    $previewController = new PreviewController();
    $previewController->preview($_GET['file']);
    exit;
}

if ($uri === BASE_URL . '/uploads') {
    $fileController = new FileController();
    $fileController->upload();
    exit;
}

?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($translator->get('login_title')); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($translator->get('login_title')); ?></h1>
    </header>
    <main>
        <!-- Formulario para subir el archivo ODS -->
        <form action="<?php echo BASE_URL; ?>/uploads" method="post" enctype="multipart/form-data">
            <div>
                <label for="ods_file"><?php echo htmlspecialchars($translator->get('enter_ods_url')); ?></label>
                <input type="file" name="ods_file" id="ods_file" required>
            </div>
            <div>
                <button type="submit"><?php echo htmlspecialchars($translator->get('login_button')); ?></button>
            </div>
        </form>

        <!-- Sección para listar los archivos subidos -->
        <section>
            <h2>Archivos subidos</h2>
            <ul>
                <?php
                $uploadsDir = __DIR__ . '/uploads/';
                if (is_dir($uploadsDir)) {
                    $files = array_diff(scandir($uploadsDir), array('..', '.'));
                    foreach ($files as $file) {
                        echo '<li><a href="' . BASE_URL . '/preview?file=' . urlencode($file) . '">' . htmlspecialchars($file) . '</a></li>';
                    }
                } else {
                    echo '<li>No hay archivos subidos.</li>';
                }
                ?>
            </ul>
        </section>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> - <?php echo htmlspecialchars($translator->get('dashboard_title')); ?></p>
    </footer>
    <script src="js/main.js"></script>
</body>
</html>