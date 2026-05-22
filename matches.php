<?php
/*
 * matches.php
 * Lista de oponentes que el usuario ha aceptado desde la pantalla "Descubrir".
 * Por simplicidad los matches se guardan en sesión (sin tabla nueva).
 */

session_start();

// Bloqueamos el acceso si no hay sesión iniciada
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Conexión a la base de datos
require_once 'conexion.php';

// Recuperamos los IDs de matches guardados en sesión
$matches = $_SESSION['matches'] ?? [];
$listado = [];

// Si hay matches, consultamos sus datos en la base de datos
if (!empty($matches)) {
    $placeholders = implode(',', array_fill(0, count($matches), '?'));
    $sql = "SELECT id, nombre, ciudad, experiencia, telefono, instagram
            FROM usuarios
            WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($matches);
    $listado = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matches - Sparring</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="contenedor contenedor-app">
        <h1>Matches</h1>

        <!-- Mensaje si aún no hay matches, o listado de matches -->
        <?php if (empty($listado)): ?>
            <p class="texto-centro">Aún no tienes matches. ¡Sigue descubriendo oponentes!</p>
        <?php else: ?>
            <ul class="lista-matches">
                <?php foreach ($listado as $m): ?>
                    <li class="item-match">
                        <!-- Avatar con la inicial del nombre -->
                        <div class="avatar-mini"><?= htmlspecialchars(mb_substr($m['nombre'], 0, 1)) ?></div>
                        <div class="info-match">
                            <strong><?= htmlspecialchars($m['nombre']) ?></strong>
                            <span><?= htmlspecialchars($m['experiencia']) ?> · <?= htmlspecialchars($m['ciudad']) ?></span>

                            <?php
                            // Mostramos solo los datos de contacto que el oponente haya rellenado
                            $tieneContacto = !empty($m['telefono']) || !empty($m['instagram']);
                            ?>
                            <!-- Datos de contacto: solo se muestran si el oponente los tiene -->
                            <?php if ($tieneContacto): ?>
                                <div class="contacto-match">
                                    <?php if (!empty($m['telefono'])): ?>
                                        <span>📞 <?= htmlspecialchars($m['telefono']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($m['instagram'])): ?>
                                        <span>📷 @<?= htmlspecialchars($m['instagram']) ?></span>
                                    <?php endif; ?>

                                </div>
                            <?php else: ?>
                                <small class="sin-contacto">No ha compartido contacto todavía.</small>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- Barra de navegación inferior -->
        <nav class="barra-nav">
            <a href="descubrir.php" class="nav-item">Descubrir</a>
            <a href="matches.php"   class="nav-item activo">Matches</a>
            <a href="perfil.php"    class="nav-item">Perfil</a>
        </nav>
    </div>
</body>
</html>
