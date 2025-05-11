<?php
session_start();
require_once 'i18n.php';
require 'funciones/odsasqlite.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_step']) && $_POST['import_step'] === '1') {
    // Paso 1: Procesar la importación del ODS
    $url = $_POST['url'];
    $dbName = trim($_POST['nombre']);
    if (empty($url) || empty($dbName)) {
        $error = t("error_both_required");
    } else {
        // Importar el archivo ODS
        odsasqlite($url, $dbName);
        // Crear el archivo de configuración
        $configContent = "<?php\n\$config = [\n    'db_name' => '" . addslashes($dbName) . ".db'\n];\n";
        file_put_contents("config.php", $configContent);
        // Crear tabla de usuarios y semilla un usuario por defecto
        $db = new SQLite3($dbName . ".db");
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT,
            username TEXT,
            password TEXT
        )");
        $check = $db->query("SELECT COUNT(*) as count FROM users WHERE username = 'defaultuser'");
        $row = $check->fetchArray(SQLITE3_ASSOC);
        if ($row['count'] == 0) {
            $db->exec("INSERT INTO users (name, email, username, password) VALUES (
                'Default User',
                'user@example.com',
                'defaultuser',
                'defaultpass'
            )");
        }
        // Crear tablas para departamentos
        $db->exec("CREATE TABLE IF NOT EXISTS departments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT
        )");
        $db->exec("CREATE TABLE IF NOT EXISTS department_tables (
            department_id INTEGER,
            table_name TEXT
        )");
        // Pasar al Paso 2: creación de departamentos
        $_SESSION['installer_dbname'] = $dbName . ".db";
        header("Location: importador.php?step=2");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_step']) && $_POST['import_step'] === '2') {
    $dbName = $_SESSION['installer_dbname'] ?? null;
    if (!$dbName) {
        header("Location: importador.php");
        exit;
    }
    $db = new SQLite3($dbName);
    // Insertar el nuevo departamento
    $departmentName = trim($_POST['department_name']);
    if (!empty($departmentName)) {
        $stmt = $db->prepare("INSERT INTO departments (name) VALUES (:name)");
        $stmt->bindValue(':name', $departmentName, SQLITE3_TEXT);
        $stmt->execute();
        $departmentId = $db->lastInsertRowID();
        // Insertar las relaciones de tablas seleccionadas
        if (isset($_POST['tables']) && is_array($_POST['tables'])) {
            foreach ($_POST['tables'] as $tableName) {
                $stmt2 = $db->prepare("INSERT INTO department_tables (department_id, table_name) VALUES (:depId, :tbl)");
                $stmt2->bindValue(':depId', $departmentId, SQLITE3_INTEGER);
                $stmt2->bindValue(':tbl', $tableName, SQLITE3_TEXT);
                $stmt2->execute();
            }
        }
        $success = t("Department created successfully!");
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo t("importer_title"); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="header">
    <div id="corporativo">
        <img src="logo.png" alt="Logo">
        <h1><?php echo t("importer_title"); ?></h1>
    </div>
</div>

<?php if (!isset($_GET['step'])): ?>
    <!-- Paso 1: Formulario de importación ODS -->
    <div class="importador-container">
        <?php if(isset($error)): ?>
            <p class="message-error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="importador.php" method="POST">
            <input type="hidden" name="import_step" value="1">
            <h3><?php echo t("importer_heading"); ?></h3>
            <p class="importador-description">
                <?php echo t("importer_description") . " Note: Extended foreign key support enabled."; ?>
            </p>
            <div class="form-group">
                <label><?php echo t("enter_ods_url"); ?></label>
                <input type="url" name="url" required>
            </div>
            <div class="form-group">
                <label><?php echo t("enter_db_name"); ?></label>
                <input type="text" name="nombre" required>
            </div>
            <input type="submit" value="Import" class="btn-submit">
            <p class="example-url">
                Example URL: https://example.com/path/to/file.ods
            </p>
        </form>
    </div>

<?php elseif ($_GET['step'] == 2): ?>
    <!-- Paso 2: Formulario de creación de Departamento -->
    <?php
    $dbName = $_SESSION['installer_dbname'] ?? null;
    if ($dbName) {
        $db = new SQLite3($dbName);
        $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        $tables = [];
        while($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if (!in_array($row['name'], ['departments','department_tables','users'])) {
                $tables[] = $row['name'];
            }
        }
    }
    ?>
    <div class="importador-container">
        <?php if(isset($success)): ?>
            <p class="message-success"><?php echo $success; ?></p>
        <?php endif; ?>
        <h3>Create a Department</h3>
        <form action="importador.php?step=2" method="POST">
            <input type="hidden" name="import_step" value="2">
            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="department_name" required>
            </div>
            <div class="form-group">
                <label>Select the tables that belong to this Department:</label><br>
                <?php foreach ($tables as $tbl): ?>
                    <input type="checkbox" name="tables[]" value="<?php echo htmlspecialchars($tbl); ?>"> 
                    <?php echo htmlspecialchars($tbl); ?><br>
                <?php endforeach; ?>
            </div>
            <input type="submit" value="Add Department" class="btn-submit">
        </form>
        <hr>
        <p>When you’re done creating departments, <a href="index.php" class="btn">Go to Dashboard</a></p>
    </div>
<?php endif; ?>
</body>
</html>