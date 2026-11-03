<?php
require  __DIR__ .'/../../config/database.php';
$recibir_admin = "SELECT id_funcionario, nombre, apellido, email, es_medico, estado_activo, usuario, fecha_registro FROM ADMINISTRATIVO";
$stmt = $pdo->query($recibir_admin);

$admin = $stmt->fetchAll(PDO::FETCH_ASSOC);
