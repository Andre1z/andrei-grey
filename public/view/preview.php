<?php
// public/view/preview.php

// Si no se recibe el parámetro "file", redirige a index.php
if (!isset($_GET['file']) || empty($_GET['file'])) {
    header("Location: ../index.php");
    exit;
}

$fileName = $_GET['file']; // Recibe el nombre del archivo vía GET
// Definir la ruta de la carpeta de uploads (según la estructura de tu proyecto)
$uploadsDir = __DIR__ . '/../uploads/';
$filePath = $uploadsDir . $fileName;

if (!file_exists($filePath)) {
    echo "Archivo no encontrado: " . htmlspecialchars($fileName);
    exit;
}

// Incluir el autoloader de Composer para usar PhpSpreadsheet
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Ods;

$reader = new Ods();
try {
    $spreadsheet = $reader->load($filePath);
} catch (Exception $e) {
    echo "Error al leer el archivo: " . $e->getMessage();
    exit;
}

// Convertir la hoja activa a un array para su visualización
$data = $spreadsheet->getActiveSheet()->toArray();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vista Previa – <?php echo htmlspecialchars($fileName); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Ajusta la ruta del CSS según corresponda -->
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
    <h1>Vista Previa del Archivo: <?php echo htmlspecialchars($fileName); ?></h1>
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
        <tr><td colspan="100%">No hay datos para mostrar.</td></tr>
      <?php endif; ?>
    </table>
  </main>
  <footer>
    <!-- Botón para volver a index.php -->
    <a href="../index.php">Volver al inicio</a>
  </footer>
</body>
</html>