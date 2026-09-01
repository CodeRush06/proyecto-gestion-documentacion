<?php
require "conectar.php";

$recibir_admin = "SELECT nombre FROM ADMINISTRATIVO";
$stmt = $pdo->query($recibir_admin);

$admin = $stmt->fetchAll(PDO::FETCH_ASSOC);
