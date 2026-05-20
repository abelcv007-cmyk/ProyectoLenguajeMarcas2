<?php
/*
 * index.php
 * Punto de entrada del sistema. Su única función es redirigir al usuario
 * a la página correspondiente dependiendo de si ya inició sesión o no.
 */

// Iniciamos la sesión para poder leer las variables de sesión
session_start();

// Si el usuario ya está autenticado (existe la variable $_SESSION['usuario_id']),
// lo enviamos directamente a su página de perfil.
if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}

// Si no ha iniciado sesión, lo redirigimos al formulario de login.
header('Location: login.php');
exit;
