<?php
// listar_formularios.php
require __DIR__ . "/conectar.php";

$sql = "SELECT e.*, te.nombre_tipo
        FROM ENCUESTA e
        JOIN TIPO_ENCUESTA te ON e.id_tipo_encuesta = te.id_tipo_encuesta";
$stmt = $pdo->query($sql);
$encuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);