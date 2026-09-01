<?php
require "conectar.php";

$recibir_user = "SELECT nombre FROM USUARIO";
$stmt = $pdo->query($recibir_user);

$user = $stmt->fetchAll(PDO::FETCH_ASSOC);

