<?php
session_start();

// Incluir la configuración para tener definida BASE_URL
require_once __DIR__ . '/../../app/config.php';

// Obtener el idioma desde la sesión (por defecto "en")
$language = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';

// Incluir el autoloader de Composer para usar librerías y la clase Translation
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Helpers\Translation;
use PhpOffice\PhpSpreadsheet\Reader\Ods;

// Instanciar la clase de traducciones usando el archivo CSV ubicado en la carpeta raíz (por ejemplo, /translations/)
$translator = new Translation(__DIR__ . '/../../translations/translations.csv', $language);

// Verificar que se reciba el parámetro "file"
if (!isset($_GET['file']) || empty($_GET['file'])) {
    header("Location: ../index.php");
    exit;
}

$fileName = $_GET['file'];
$uploadsDir = __DIR__ . '/../uploads/';
$filePath = $uploadsDir . $fileName;

// Si el archivo no existe, se muestra un mensaje usando la traducción 'file_not_found'
if (!file_exists($filePath)) {
    echo $translator->get('file_not_found') . " " . htmlspecialchars($fileName);
    exit;
}

$reader = new Ods();
try {
    $spreadsheet = $reader->load($filePath);
} catch (Exception $e) {
    die("Error al cargar el archivo: " . $e->getMessage());
}

$data = $spreadsheet->getActiveSheet()->toArray();
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($translator->get('preview_title')); ?> – <?php echo htmlspecialchars($fileName); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <style>
        /* Estilos básicos para la tabla de previsualización */
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px auto;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eaeaea;
        }
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($translator->get('preview_title')); ?> – <?php echo htmlspecialchars($fileName); ?></h1>
    </header>
    <main>
        <table>
            <?php if (is_array($data) && count($data) > 0): ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td><?php echo htmlspecialchars((string)$cell); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="100%"><?php echo htmlspecialchars($translator->get('no_data')); ?></td></tr>
            <?php endif; ?>
        </table>
    </main>
    <footer>
        <a href="../index.php"><?php echo htmlspecialchars($translator->get('dashboard_title')); ?></a>
    </footer>
</body>
</html>