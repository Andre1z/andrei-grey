<?php
// public/login.php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';

use App\Helpers\Translation;

// Obtener el idioma, por ejemplo de una variable GET, por defecto inglés
$language = isset($_GET['lang']) ? $_GET['lang'] : 'en';
$translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);

// Ruta donde se guardarán los usuarios
$usersFile = __DIR__ . '/../storage/users.json';

// Inicializamos el mensaje
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Cargamos usuarios del archivo JSON
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
    } else {
        $users = [];
    }

    // Validamos que el usuario exista y verficamos la contraseña
    if (isset($users[$username])) {
        if (password_verify($password, $users[$username]['password'])) {
            // Credenciales correctas; se inicia sesión y se redirige
            $_SESSION['username'] = $username;
            header("Location: " . BASE_URL . "/index.php");
            exit;
        } else {
            $message = $translator->get('login_invalid_credentials');
        }
    } else {
        $message = $translator->get('login_invalid_credentials');
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
</head>
<body>
    <h1><?php echo htmlspecialchars($translator->get('login_title')); ?></h1>
    <?php if (!empty($message)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="login.php?lang=<?php echo htmlspecialchars($language); ?>" method="post">
        <label for="username"><?php echo htmlspecialchars($translator->get('login_username')); ?></label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password"><?php echo htmlspecialchars($translator->get('login_password')); ?></label>
        <input type="password" name="password" id="password" required>
        <br>
        <button type="submit"><?php echo htmlspecialchars($translator->get('login_button')); ?></button>
    </form>
    <p>
        <?php echo htmlspecialchars($translator->get('login_no_account')); ?> 
        <a href="register.php?lang=<?php echo htmlspecialchars($language); ?>"><?php echo htmlspecialchars($translator->get('register_link')); ?></a>
    </p>
</body>
</html>