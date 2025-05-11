<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Vista Previa – <?php echo htmlspecialchars($fileName); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/styles.css">
  <style>
    /* Estilos para la previsualización */
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
      <?php if (isset($data) && is_array($data)): ?>
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
    <a href="/">Volver al inicio</a>
  </footer>
</body>
</html>