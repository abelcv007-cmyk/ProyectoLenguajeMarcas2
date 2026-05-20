<?php
/*
 * login.php
 * Página de inicio de sesión.
 * - Si el usuario ya está autenticado, se redirige al perfil.
 * - Si llega una petición POST, valida las credenciales contra la base de datos.
 * - Si las credenciales son correctas, guarda el id del usuario en la sesión
 *   y lo redirige al perfil.
 */

session_start();

// Si ya hay sesión activa, redirigimos al perfil
if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}

// Incluimos la conexión a la base de datos
require_once 'conexion.php';

// Variable que almacenará posibles errores para mostrarlos en el formulario
$error = '';

// Procesamos el formulario solo si el método de la petición es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos y limpiamos los datos enviados
    $email      = trim($_POST['email']      ?? '');
    $contrasena = trim($_POST['contrasena'] ?? '');

    // Validaciones básicas de los campos
    if ($email === '' || $contrasena === '') {
        $error = 'Debes completar todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no tiene un formato válido.';
    } else {
        // Buscamos al usuario por email usando una sentencia preparada
        $sql = 'SELECT id, nombre, contraseña FROM usuarios WHERE email = :email LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        // Verificamos que el usuario exista y que la contraseña coincida con el hash guardado
        if ($usuario && password_verify($contrasena, $usuario['contraseña'])) {
            // Guardamos el id y nombre en la sesión
            $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];

            // Redirigimos a la página del perfil
            header('Location: perfil.php');
            exit;
        } else {
            // Mensaje genérico para no dar pistas a posibles atacantes
            $error = 'Email o contraseña incorrectos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Sparring</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="contenedor">
        <h1>Iniciar sesión</h1>

        <?php if ($error !== ''): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="formulario">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <button type="submit" class="boton">Entrar</button>
        </form>

        <p class="texto-centro">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </p>
    </div>
</body>
</html>
