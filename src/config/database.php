<?php
$host = "coderush-db";
$db = "modulo1";
$user = "coderush";
$pass = "coderush";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

} catch (PDOException $e) {
    echo "Conexión no exitosa. Motivo: " . $e->getMessage(); //Este atributo se muestra por razones de ayuda y se eliminará pronto para que el sistema sea mas seguro.
}
?>
