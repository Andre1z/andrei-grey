<?php
session_start();

// Incluir configuración y el autoloader de Composer
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Helpers\Translation;

// Determinar el idioma: se comprueba primero GET o POST; si no, se consulta la cookie "language"
if (isset($_GET['lang'])) {
    $language = $_GET['lang'];
} elseif (isset($_POST['lang'])) {
    $language = $_POST['lang'];
} elseif (isset($_COOKIE['language'])) {
    $language = $_COOKIE['language'];
} else {
    $language = 'en';
}

// Instanciar la clase Translation usando el CSV de traducciones localizado en la carpeta "translations" (nivel raíz)
$translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);

// Ubicación del archivo de usuarios (almacenados en un JSON dentro de la carpeta storage)
$usersFile = __DIR__ . '/../storage/users.json';

// Inicializar el mensaje de error
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Actualizar el idioma de acuerdo al formulario (por si el usuario lo cambia mediante el select)
    $language = isset($_POST['lang']) ? $_POST['lang'] : $language;
    // Reinstanciar el traductor en caso de cambio de idioma
    $translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Cargar los usuarios desde el archivo JSON (si existe)
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
    } else {
        $users = [];
    }
    
    // Verificar las credenciales
    if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
        // Credenciales correctas: guardar el nombre de usuario y el idioma seleccionado en la sesión
        $_SESSION['username'] = $username;
        $_SESSION['language'] = $language;
        // Actualizar la cookie para persistir el idioma durante 30 días
        setcookie('language', $language, time() + (86400 * 30), "/");
        header("Location: " . BASE_URL . "/index.php");
        exit;
    } else {
        $message = $translator->get('invalid_credentials');
        // También actualizamos la cookie con el idioma seleccionado (aunque el login falle)
        setcookie('language', $language, time() + (86400 * 30), "/");
    }
} else {
    // Para peticiones GET, se asegura que la cookie se actualice si no existe
    if (!isset($_COOKIE['language'])) {
        setcookie('language', $language, time() + (86400 * 30), "/");
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($translator->get('login_title')); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/auth.css">
    <style>
        .lang-select {
            margin-left: 10px;
            padding: 5px;
        }
    </style>
</head>
<body class="auth">
    <h1><?php echo htmlspecialchars($translator->get('login_title')); ?></h1>
    <?php if (!empty($message)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="login.php" method="post">
        <label for="username"><?php echo htmlspecialchars($translator->get('username')); ?></label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password"><?php echo htmlspecialchars($translator->get('password')); ?></label>
        <input type="password" name="password" id="password" required>
        <br>
        <!-- Select para elegir el idioma -->
        <label for="lang">Idioma:</label>
        <select name="lang" id="lang" class="lang-select">
            <option value="en" <?php if($language === 'en') echo "selected"; ?>>English</option>
            <option value="es" <?php if($language === 'es') echo "selected"; ?>>Español</option>
            <option value="fr" <?php if($language === 'fr') echo "selected"; ?>>Français</option>
            <option value="de" <?php if($language === 'de') echo "selected"; ?>>Deutsch</option>
            <option value="it" <?php if($language === 'it') echo "selected"; ?>>Italiano</option>
            <option value="ja" <?php if($language === 'ja') echo "selected"; ?>>日本語</option>
            <option value="ko" <?php if($language === 'ko') echo "selected"; ?>>한국어</option>
            <option value="zh" <?php if($language === 'zh') echo "selected"; ?>>中文</option>
        </select>
        <br>
        <button type="submit"><?php echo htmlspecialchars($translator->get('login_button')); ?></button>
    </form>
    <p>
        <?php echo htmlspecialchars($translator->get('login_no_account')); ?> 
        <a href="register.php?lang=<?php echo htmlspecialchars($language); ?>">
            <?php echo htmlspecialchars($translator->get('register_link')); ?>
        </a>
    </p>
</body>
</html>