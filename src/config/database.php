<?php
$host = "localhost";
$db = "modulo1";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // CORREGIDO: PDO::ERRMODE_EXCEPTION
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Conexión exitosa a la base sigsm.<br>";

} catch (PDOException $e) {
    // Es mejor mostrar el error real para saber por qué falla (ej. contraseña incorrecta, base de datos no existe)
    echo "Conexión no exitosa. Motivo: " . $e->getMessage();
}
?>
