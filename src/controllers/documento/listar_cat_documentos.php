<?php
require_once __DIR__ . '/../../config/database.php';

$recibir_categoria = "SELECT * FROM CATEGORIA_DOCUMENTO";
$stmt = $pdo->query($recibir_categoria);

$categoria = $stmt->fetchAll(PDO::FETCH_ASSOC);
