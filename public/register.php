<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config.php';

use App\Helpers\Translation;

// Se determina el idioma; por defecto "en"
$language = isset($_GET['lang']) ? $_GET['lang'] : 'en';
$translator = new Translation(__DIR__ . '/../translations/translations.csv', $language);

// Ubicación del archivo de usuarios
$usersFile = __DIR__ . '/../storage/users.json';
$message = '';

// Procesamos el formulario de registro vía POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Verificar que no haya campos vacíos
    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $message = $translator->get('register_fill_fields');
    } elseif ($password !== $confirmPassword) {
        $message = $translator->get('register_password_mismatch');
    } else {
        // Cargar usuarios existentes (si el archivo existe)
        if (file_exists($usersFile)) {
            $users = json_decode(file_get_contents($usersFile), true);
        } else {
            $users = [];
        }
        // Comprobar si el nombre de usuario ya existe
        if (isset($users[$username])) {
            $message = $translator->get('register_user_exists');
        } else {
            // Hashear la contraseña y guardar los datos del usuario
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $users[$username] = [
                'username' => $username,
                'password' => $hashedPassword
            ];
            if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT))) {
                // Registro exitoso; redirige al login
                header("Location: login.php?lang=" . urlencode($language));
                exit;
            } else {
                $message = $translator->get('register_error_saving');
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($language); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($translator->get('register_title')); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/styles.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($translator->get('register_title')); ?></h1>
    <?php if (!empty($message)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="register.php?lang=<?php echo htmlspecialchars($language); ?>" method="post">
        <label for="username"><?php echo htmlspecialchars($translator->get('register_username')); ?></label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password"><?php echo htmlspecialchars($translator->get('register_password')); ?></label>
        <input type="password" name="password" id="password" required>
        <br>
        <label for="confirm_password"><?php echo htmlspecialchars($translator->get('register_confirm_password')); ?></label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <br>
        <button type="submit"><?php echo htmlspecialchars($translator->get('register_button')); ?></button>
    </form>
    <p>
        <?php echo htmlspecialchars($translator->get('register_have_account')); ?>  
        <a href="login.php?lang=<?php echo htmlspecialchars($language); ?>">
            <?php echo htmlspecialchars($translator->get('login_link')); ?>
        </a>
    </p>
</body>
</html>