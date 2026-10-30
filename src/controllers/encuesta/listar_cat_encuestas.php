<?php
require  __DIR__ .'/../../config/database.php';
$recibir_enc = "SELECT * FROM TIPO_ENCUESTA";
$stmt = $pdo->query($recibir_enc);

$tipo_encuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
