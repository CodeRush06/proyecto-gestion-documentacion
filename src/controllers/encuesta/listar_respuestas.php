<?php

$sql = "SELECT re.id_respuesta, e.titulo, re.fecha_respuesta, re.anonima
        FROM RESPUESTA_ENCUESTA re
        JOIN ENCUESTA e ON re.id_encuesta = e.id_encuesta
        ORDER BY re.fecha_respuesta DESC";
$stmt = $pdo->query($sql);
$respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
