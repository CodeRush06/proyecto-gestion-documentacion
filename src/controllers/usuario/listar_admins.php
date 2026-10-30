<?php
require __DIR__ . '/../../config/database.php;

$recibir_admin = "SELECT nombre FROM ADMINISTRATIVO";
$stmt = $pdo->query($recibir_admin);

$admin = $stmt->fetchAll(PDO::FETCH_ASSOC);
