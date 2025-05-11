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

// Ruteo para /upload usando el controlador
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($uri === BASE_URL . '/view') {
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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
  <header>
    <h1><?php echo htmlspecialchars($translator->get('login_title')); ?></h1>
  </header>
  <main>
    <!-- Formulario para subir el archivo ODS -->
    <form action="<?php echo BASE_URL; ?>/view" method="post" enctype="multipart/form-data">
      <div>
        <label for="ods_file"><?php echo htmlspecialchars($translator->get('enter_ods_url')); ?></label>
        <input type="file" name="ods_file" id="ods_file" required>
      </div>
      <div>
        <button type="submit"><?php echo htmlspecialchars($translator->get('login_button')); ?></button>
      </div>
    </form>

    <!-- Sección para listar los archivos subidos y redirigir a la vista de previsualización -->
    <section>
      <h2>Archivos subidos</h2>
      <ul>
        <?php
        // Obtiene la ruta física a la carpeta de uploads
        $uploadsDir = __DIR__ . '/uploads/';
        if (is_dir($uploadsDir)) {
          $files = array_diff(scandir($uploadsDir), array('..', '.'));
          foreach ($files as $file) {
            // Genera el enlace que apunta directamente a /view/preview.php
            echo '<li><a href="' . BASE_URL . '/view/preview.php?file=' . urlencode($file) . '">' . htmlspecialchars($file) . '</a></li>';
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
  <script src="<?php echo BASE_URL; ?>/js/main.js"></script>
</body>
</html>