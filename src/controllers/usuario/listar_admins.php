<?php
require  __DIR__ .'/../../config/database.php';
$recibir_admin = "SELECT id_funcionario, nombre, apellido, email, es_medico, usuario, fecha_registro, estado_activo FROM ADMINISTRATIVO WHERE estado_activo=1 ";
$stmt = $pdo->query($recibir_admin);

$admin = $stmt->fetchAll(PDO::FETCH_ASSOC);
