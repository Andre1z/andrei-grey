<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Previsualización de <?php echo htmlspecialchars($fileName); ?></title>
  <link rel="stylesheet" href="/css/styles.css">
  <style>
    /* Estilos rápidos para la tabla de previsualización */
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
    <h1>Vista Preliminar del archivo: <?php echo htmlspecialchars($fileName); ?></h1>
  </header>
  <main>
    <table>
      <?php foreach ($data as $row): ?>
        <tr>
          <?php foreach ($row as $cell): ?>
            <td><?php echo htmlspecialchars((string)$cell); ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </table>
  </main>
  <footer>
    <a href="/">Volver al inicio</a>
  </footer>
</body>
</html>