<?php
/*
 * registro.php
 * Página para registrar un nuevo usuario.
 * - Valida los datos del formulario.
 * - Comprueba que el email no exista ya.
 * - Guarda al usuario con la contraseña hasheada usando password_hash().
 * - Si el registro es correcto, redirige al login.
 */

session_start();

// Si ya hay sesión activa, no tiene sentido mostrar el registro
if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}

// Conexión a la base de datos
require_once 'conexion.php';

// Variables para mostrar mensajes y conservar valores del formulario
$error  = '';
$exito  = '';
$datos  = [
    'nombre'      => '',
    'email'       => '',
    'genero'      => '',
    'experiencia' => '',
    'peso'        => '',
    'ciudad'      => '',
];

// Procesamos el formulario solo si llega por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos los datos del formulario
    $datos['nombre']      = trim($_POST['nombre']      ?? '');
    $datos['email']       = trim($_POST['email']       ?? '');
    $datos['genero']      = trim($_POST['genero']      ?? '');
    $datos['experiencia'] = trim($_POST['experiencia'] ?? '');
    $datos['peso']        = trim($_POST['peso']        ?? '');
    $datos['ciudad']      = trim($_POST['ciudad']      ?? '');
    $contrasena           = $_POST['contrasena']        ?? '';
    $contrasena2          = $_POST['contrasena2']       ?? '';

    // Validaciones
    if ($datos['nombre'] === '' || $datos['email'] === '' || $contrasena === ''
        || $datos['genero'] === '' || $datos['experiencia'] === ''
        || $datos['peso'] === '' || $datos['ciudad'] === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no tiene un formato válido.';
    } elseif (strlen($contrasena) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($contrasena !== $contrasena2) {
        $error = 'Las contraseñas no coinciden.';
    } elseif (!is_numeric($datos['peso']) || $datos['peso'] <= 0) {
        $error = 'El peso debe ser un número válido.';
    } else {
        // Comprobamos si el email ya está registrado
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $datos['email']]);

        if ($stmt->fetch()) {
            $error = 'Ese email ya está registrado.';
        } else {
            // Hasheamos la contraseña antes de guardarla (nunca se guarda en texto plano)
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            // Insertamos al usuario con una sentencia preparada
            $sql = 'INSERT INTO usuarios (nombre, email, contraseña, género, experiencia, peso, ciudad)
                    VALUES (:nombre, :email, :contrasena, :genero, :experiencia, :peso, :ciudad)';

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre'      => $datos['nombre'],
                ':email'       => $datos['email'],
                ':contrasena'  => $hash,
                ':genero'      => $datos['genero'],
                ':experiencia' => $datos['experiencia'],
                ':peso'        => $datos['peso'],
                ':ciudad'      => $datos['ciudad'],
            ]);

            // Mostramos mensaje de éxito y limpiamos los datos del formulario
            $exito = '¡Cuenta creada con éxito! Ya puedes iniciar sesión.';
            $datos = array_fill_keys(array_keys($datos), '');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Sparring</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="contenedor">
        <h1>Crear cuenta</h1>

        <?php if ($error !== ''): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($exito !== ''): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div>
        <?php endif; ?>

        <form action="registro.php" method="POST" class="formulario">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required
                   value="<?= htmlspecialchars($datos['nombre']) ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($datos['email']) ?>">

            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required minlength="6">

            <label for="contrasena2">Repetir contraseña</label>
            <input type="password" id="contrasena2" name="contrasena2" required minlength="6">

            <label for="genero">Género</label>
            <select id="genero" name="genero" required>
                <option value="">-- Selecciona --</option>
                <option value="Masculino"  <?= $datos['genero'] === 'Masculino'  ? 'selected' : '' ?>>Masculino</option>
                <option value="Femenino"   <?= $datos['genero'] === 'Femenino'   ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro"       <?= $datos['genero'] === 'Otro'       ? 'selected' : '' ?>>Otro</option>
            </select>

            <label for="experiencia">Experiencia</label>
            <select id="experiencia" name="experiencia" required>
                <option value="">-- Selecciona --</option>
                <option value="Principiante" <?= $datos['experiencia'] === 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                <option value="Intermedio"   <?= $datos['experiencia'] === 'Intermedio'   ? 'selected' : '' ?>>Intermedio</option>
                <option value="Avanzado"     <?= $datos['experiencia'] === 'Avanzado'     ? 'selected' : '' ?>>Avanzado</option>
                <option value="Profesional"  <?= $datos['experiencia'] === 'Profesional'  ? 'selected' : '' ?>>Profesional</option>
            </select>

            <label for="peso">Peso (kg)</label>
            <input type="number" id="peso" name="peso" step="0.1" min="0" required
                   value="<?= htmlspecialchars($datos['peso']) ?>">

            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" required
                   value="<?= htmlspecialchars($datos['ciudad']) ?>">

            <button type="submit" class="boton">Registrarse</button>
        </form>

        <p class="texto-centro">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
        </p>
    </div>
</body>
</html>
