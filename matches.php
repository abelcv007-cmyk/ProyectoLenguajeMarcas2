<?php
/*
 * matches.php
 * Lista de oponentes que el usuario ha aceptado desde la pantalla "Descubrir".
 * Por simplicidad los matches se guardan en sesión (sin tabla nueva).
 */

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexion.php';

$matches = $_SESSION['matches'] ?? [];
$listado = [];

if (!empty($matches)) {
    $placeholders = implode(',', array_fill(0, count($matches), '?'));
    $sql = "SELECT id, nombre, ciudad, experiencia
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

        <?php if (empty($listado)): ?>
            <p class="texto-centro">Aún no tienes matches. ¡Sigue descubriendo oponentes!</p>
        <?php else: ?>
            <ul class="lista-matches">
                <?php foreach ($listado as $m): ?>
                    <li class="item-match">
                        <div class="avatar-mini"><?= htmlspecialchars(mb_substr($m['nombre'], 0, 1)) ?></div>
                        <div class="info-match">
                            <strong><?= htmlspecialchars($m['nombre']) ?></strong>
                            <span><?= htmlspecialchars($m['experiencia']) ?> · <?= htmlspecialchars($m['ciudad']) ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <nav class="barra-nav">
            <a href="descubrir.php" class="nav-item">Descubrir</a>
            <a href="matches.php"   class="nav-item activo">Matches</a>
            <a href="perfil.php"    class="nav-item">Perfil</a>
        </nav>
    </div>
</body>
</html>
