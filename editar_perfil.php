<?php
/*
 * editar_perfil.php
 * Página que permite al usuario autenticado actualizar sus datos personales.
 * - Si no hay sesión iniciada, se redirige al login.
 * - Por GET muestra el formulario con los datos actuales del usuario.
 * - Por POST valida y guarda los cambios en la base de datos.
 *
 * Nota: la contraseña se actualiza solo si el usuario escribe una nueva.
 */

session_start();

// Si no hay sesión, redirigimos al login
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexion.php';

$error = '';
$exito = '';

// Cargamos los datos actuales del usuario para mostrarlos en el formulario
$stmt = $pdo->prepare('SELECT nombre, email, género, experiencia, peso, ciudad,
                              telefono, instagram
                       FROM usuarios WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Si el usuario ya no existe en la BD, cerramos la sesión
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Procesamos el formulario solo si llega por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recogemos los nuevos valores del formulario
    $nombre       = trim($_POST['nombre']      ?? '');
    $email        = trim($_POST['email']       ?? '');
    $genero       = trim($_POST['genero']      ?? '');
    $experiencia  = trim($_POST['experiencia'] ?? '');
    $peso         = trim($_POST['peso']        ?? '');
    $ciudad       = trim($_POST['ciudad']      ?? '');
    $telefono     = trim($_POST['telefono']    ?? '');
    $instagram    = trim($_POST['instagram']   ?? '');
    $contrasena   = $_POST['contrasena']        ?? '';
    $contrasena2  = $_POST['contrasena2']       ?? '';

    // Validaciones básicas
    if ($nombre === '' || $email === '' || $genero === ''
        || $experiencia === '' || $peso === '' || $ciudad === '') {
        $error = 'Todos los campos son obligatorios (excepto la contraseña si no quieres cambiarla).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El email no tiene un formato válido.';
    } elseif (!is_numeric($peso) || $peso <= 0) {
        $error = 'El peso debe ser un número válido.';
    } elseif ($contrasena !== '' && strlen($contrasena) < 6) {
        $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
    } elseif ($contrasena !== $contrasena2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Verificamos que el nuevo email no esté usado por otro usuario
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email AND id <> :id LIMIT 1');
        $stmt->execute([':email' => $email, ':id' => $_SESSION['usuario_id']]);

        if ($stmt->fetch()) {
            $error = 'Ese email ya pertenece a otra cuenta.';
        } else {
            // Parámetros comunes (incluyendo los contactos opcionales como NULL si están vacíos)
            $parametros = [
                ':nombre'      => $nombre,
                ':email'       => $email,
                ':genero'      => $genero,
                ':experiencia' => $experiencia,
                ':peso'        => $peso,
                ':ciudad'      => $ciudad,
                ':telefono'    => $telefono  !== '' ? $telefono  : null,
                ':instagram'   => $instagram !== '' ? $instagram : null,
                ':id'          => $_SESSION['usuario_id'],
            ];

            // Si el usuario quiere cambiar la contraseña, la incluimos en el UPDATE
            if ($contrasena !== '') {
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $sql = 'UPDATE usuarios
                        SET nombre = :nombre,
                            email = :email,
                            contraseña = :contrasena,
                            género = :genero,
                            experiencia = :experiencia,
                            peso = :peso,
                            ciudad = :ciudad,
                            telefono = :telefono,
                            instagram = :instagram
                        WHERE id = :id';

                $parametros[':contrasena'] = $hash;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($parametros);
            } else {
                // Si no hay contraseña nueva, no actualizamos ese campo
                $sql = 'UPDATE usuarios
                        SET nombre = :nombre,
                            email = :email,
                            género = :genero,
                            experiencia = :experiencia,
                            peso = :peso,
                            ciudad = :ciudad,
                            telefono = :telefono,
                            instagram = :instagram
                        WHERE id = :id';

                $stmt = $pdo->prepare($sql);
                $stmt->execute($parametros);
            }

            // Actualizamos también el nombre guardado en la sesión
            $_SESSION['usuario_nombre'] = $nombre;

            // Refrescamos los datos para mostrar lo guardado
            $usuario = [
                'nombre'      => $nombre,
                'email'       => $email,
                'género'      => $genero,
                'experiencia' => $experiencia,
                'peso'        => $peso,
                'ciudad'      => $ciudad,
                'telefono'    => $telefono,
                'instagram'   => $instagram,
            ];

            $exito = 'Tus datos se han actualizado correctamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar perfil - Sparring</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="contenedor">
        <h1>Editar perfil</h1>

        <?php if ($error !== ''): ?>
            <div class="mensaje-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($exito !== ''): ?>
            <div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div>
        <?php endif; ?>

        <form action="editar_perfil.php" method="POST" class="formulario">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required
                   value="<?= htmlspecialchars($usuario['nombre']) ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($usuario['email']) ?>">

            <label for="genero">Género</label>
            <select id="genero" name="genero" required>
                <option value="Masculino" <?= $usuario['género'] === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                <option value="Femenino"  <?= $usuario['género'] === 'Femenino'  ? 'selected' : '' ?>>Femenino</option>
                <option value="Otro"      <?= $usuario['género'] === 'Otro'      ? 'selected' : '' ?>>Otro</option>
            </select>

            <label for="experiencia">Experiencia</label>
            <select id="experiencia" name="experiencia" required>
                <option value="Principiante" <?= $usuario['experiencia'] === 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                <option value="Intermedio"   <?= $usuario['experiencia'] === 'Intermedio'   ? 'selected' : '' ?>>Intermedio</option>
                <option value="Avanzado"     <?= $usuario['experiencia'] === 'Avanzado'     ? 'selected' : '' ?>>Avanzado</option>
                <option value="Profesional"  <?= $usuario['experiencia'] === 'Profesional'  ? 'selected' : '' ?>>Profesional</option>
            </select>

            <label for="peso">Peso (kg)</label>
            <input type="number" id="peso" name="peso" step="0.1" min="0" required
                   value="<?= htmlspecialchars($usuario['peso']) ?>">

            <label for="ciudad">Ciudad</label>
            <input type="text" id="ciudad" name="ciudad" required
                   value="<?= htmlspecialchars($usuario['ciudad']) ?>">

            <hr>
            <p class="nota">Datos de contacto (opcionales). Solo se mostrarán a las personas con las que hagas match.</p>

            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono"
                   value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>">

            <label for="instagram">Instagram (sin @)</label>
            <input type="text" id="instagram" name="instagram"
                   value="<?= htmlspecialchars($usuario['instagram'] ?? '') ?>">

            <hr>
            <p class="nota">Si no quieres cambiar tu contraseña, deja estos campos en blanco.</p>

            <label for="contrasena">Nueva contraseña</label>
            <input type="password" id="contrasena" name="contrasena" minlength="6">

            <label for="contrasena2">Repetir nueva contraseña</label>
            <input type="password" id="contrasena2" name="contrasena2" minlength="6">

            <button type="submit" class="boton">Guardar cambios</button>
        </form>

        <p class="texto-centro">
            <a href="perfil.php">Volver al perfil</a>
        </p>
    </div>
</body>
</html>
