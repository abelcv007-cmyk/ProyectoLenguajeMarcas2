<?php
/*
 * logout.php
 * Cierra la sesión del usuario actual y lo redirige al formulario de login.
 */

session_start();

// Eliminamos todas las variables de sesión
$_SESSION = [];

// Si se está usando una cookie de sesión, también la borramos del navegador
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Finalmente, destruimos la sesión en el servidor
session_destroy();

// Redirigimos al login
header('Location: login.php');
exit;
