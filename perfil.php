<?php
/*
 * perfil.php
 * Página que muestra los datos del usuario autenticado.
 * - Si el usuario no ha iniciado sesión, se le redirige al login.
 * - Si está autenticado, se consultan sus datos en la base de datos y se muestran.
 */

session_start();

// Bloqueamos el acceso a la página si no hay sesión iniciada
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Conexión a la base de datos
require_once 'conexion.php';

// Consultamos los datos del usuario actual con una sentencia preparada
$sql = 'SELECT nombre, email, género, experiencia, peso, ciudad,
               telefono, instagram
        FROM usuarios
        WHERE id = :id
        LIMIT 1';

$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

// Si por alguna razón el usuario ya no existe, cerramos la sesión y redirigimos
if (!$usuario) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi perfil - Sparring</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="contenedor contenedor-app">
        <h1>Mi perfil</h1>

        <p class="texto-centro">¡Bienvenido, <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>!</p>

        <!-- Tarjeta con los datos del usuario -->
        <div class="tarjeta-perfil">
            <p><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
            <p><strong>Género:</strong> <?= htmlspecialchars($usuario['género']) ?></p>
            <p><strong>Experiencia:</strong> <?= htmlspecialchars($usuario['experiencia']) ?></p>
            <p><strong>Peso:</strong> <?= htmlspecialchars($usuario['peso']) ?> kg</p>
            <p><strong>Ciudad:</strong> <?= htmlspecialchars($usuario['ciudad']) ?></p>

            <?php if (!empty($usuario['telefono']) || !empty($usuario['instagram'])): ?>
                <hr>
                <p class="nota">Contacto que verán tus matches:</p>
                <?php if (!empty($usuario['telefono'])): ?>
                    <p>📞 <?= htmlspecialchars($usuario['telefono']) ?></p>
                <?php endif; ?>
                <?php if (!empty($usuario['instagram'])): ?>
                    <p>📷 @<?= htmlspecialchars($usuario['instagram']) ?></p>
                <?php endif; ?>

            <?php endif; ?>
        </div>

        <!-- Botones de acción -->
        <div class="acciones">
            <a href="editar_perfil.php" class="boton">Editar perfil</a>
            <a href="logout.php" class="boton boton-secundario">Cerrar sesión</a>
        </div>

        <!-- Barra de navegación inferior -->
        <nav class="barra-nav">
            <a href="descubrir.php" class="nav-item">Descubrir</a>
            <a href="matches.php"   class="nav-item">Matches</a>
            <a href="perfil.php"    class="nav-item activo">Perfil</a>
        </nav>
    </div>
</body>
</html>
