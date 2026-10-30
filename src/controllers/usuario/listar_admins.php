<?php
require  __DIR__ .'/../../config/database.php';
$recibir_admin = "SELECT id_funcionario, nombre, es_medico FROM ADMINISTRATIVO";
$stmt = $pdo->query($recibir_admin);

$admin = $stmt->fetchAll(PDO::FETCH_ASSOC);
