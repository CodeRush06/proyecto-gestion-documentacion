<?php
require "conectar.php";

$recibir_enc = "SELECT * FROM tipo_encuesta";
$stmt = $pdo->query($recibir_enc);

$tipo_encuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
