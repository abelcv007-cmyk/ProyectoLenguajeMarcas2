<?php
/*
 * descubrir.php
 * Pantalla principal tipo "swipe": muestra un oponente potencial cada vez.
 * El usuario puede rechazarlo (✕) o aceptarlo (✓). Los aceptados se guardan
 * como matches en la sesión. Los ya vistos no vuelven a aparecer hasta reiniciar.
 */

session_start();

// Bloqueo si no hay sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'conexion.php';

// Inicializamos los arrays de sesión la primera vez
if (!isset($_SESSION['vistos']))   $_SESSION['vistos']   = [];
if (!isset($_SESSION['matches']))  $_SESSION['matches']  = [];

// Procesar acción (rechazar / aceptar) enviada por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oponente_id = (int)($_POST['oponente_id'] ?? 0);
    $accion      = $_POST['accion'] ?? '';

    if ($oponente_id > 0) {
        $_SESSION['vistos'][] = $oponente_id;
        if ($accion === 'aceptar' && !in_array($oponente_id, $_SESSION['matches'])) {
            $_SESSION['matches'][] = $oponente_id;
        }
    }
    header('Location: descubrir.php');
    exit;
}

// Permitir reiniciar la cola de vistos
if (isset($_GET['reiniciar'])) {
    $_SESSION['vistos'] = [];
    header('Location: descubrir.php');
    exit;
}

// Buscar el siguiente oponente: cualquier usuario distinto del actual y no visto
$excluir = array_merge([$_SESSION['usuario_id']], $_SESSION['vistos']);
$placeholders = implode(',', array_fill(0, count($excluir), '?'));

$sql = "SELECT id, nombre, género, experiencia, peso, ciudad
        FROM usuarios
        WHERE id NOT IN ($placeholders)
        ORDER BY RAND()
        LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute($excluir);
$oponente = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descubrir - Sparring</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="contenedor contenedor-app">
        <h1>Descubrir</h1>

        <?php if ($oponente): ?>
            <div class="card-oponente">
                <div class="avatar"><?= htmlspecialchars(mb_substr($oponente['nombre'], 0, 1)) ?></div>

                <h2 class="nombre-oponente"><?= htmlspecialchars($oponente['nombre']) ?></h2>
                <p class="ciudad-oponente">📍 <?= htmlspecialchars($oponente['ciudad']) ?></p>

                <div class="datos-oponente">
                    <div>
                        <span>Nivel</span>
                        <strong><?= htmlspecialchars($oponente['experiencia']) ?></strong>
                    </div>
                    <div>
                        <span>Peso</span>
                        <strong><?= htmlspecialchars($oponente['peso']) ?> kg</strong>
                    </div>
                    <div>
                        <span>Género</span>
                        <strong><?= htmlspecialchars($oponente['género']) ?></strong>
                    </div>
                </div>

                <form method="post" class="acciones-swipe">
                    <input type="hidden" name="oponente_id" value="<?= (int)$oponente['id'] ?>">
                    <button type="submit" name="accion" value="rechazar"
                            class="boton-circular boton-rechazar" title="Rechazar">✕</button>
                    <button type="submit" name="accion" value="aceptar"
                            class="boton-circular boton-aceptar" title="Aceptar sparring">✓</button>
                </form>
            </div>
        <?php else: ?>
            <div class="estado-vacio">
                <p>No hay más oponentes por ahora.</p>
                <a href="descubrir.php?reiniciar=1" class="boton">Reiniciar búsqueda</a>
            </div>
        <?php endif; ?>

        <!-- Barra de navegación inferior con los 3 apartados -->
        <nav class="barra-nav">
            <a href="descubrir.php" class="nav-item activo">Descubrir</a>
            <a href="matches.php"   class="nav-item">Matches</a>
            <a href="perfil.php"    class="nav-item">Perfil</a>
        </nav>
    </div>
</body>
</html>
