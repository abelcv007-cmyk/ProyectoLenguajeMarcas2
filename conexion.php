<?php
/*
 * conexion.php
 * Archivo encargado de establecer la conexión con la base de datos MySQL
 * mediante PDO. Se utiliza PDO porque permite el uso de sentencias preparadas
 * (prepared statements) y por lo tanto, protege contra inyecciones SQL.
 *
 * Modifica los valores de $host, $db, $user y $pass según tu entorno local.
 */

// Datos de conexión a la base de datos
$host = 'localhost';        // Servidor donde corre MySQL
$db   = 'sparring';         // Nombre de la base de datos (cámbialo si usas otro)
$user = 'root';             // Usuario de MySQL (por defecto en XAMPP es 'root')
$pass = '';                 // Contraseña de MySQL (por defecto en XAMPP está vacía)
$charset = 'utf8mb4';       // Juego de caracteres para soportar tildes y emojis

// Cadena DSN (Data Source Name) que utiliza PDO para conectarse
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones recomendadas para PDO
$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en caso de error
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve resultados como arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                   // Usa sentencias preparadas reales (más seguras)
];

try {
    // Intentamos crear la instancia de PDO
    $pdo = new PDO($dsn, $user, $pass, $opciones);
} catch (PDOException $e) {
    // Si falla la conexión, detenemos el script y mostramos un mensaje genérico
    die('Error de conexión a la base de datos: ' . $e->getMessage());
}
