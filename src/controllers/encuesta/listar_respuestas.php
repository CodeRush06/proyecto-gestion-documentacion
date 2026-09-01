<?php
// listar_respuestas.php
require __DIR__ . "/conectar.php";

$sql = "SELECT re.id_respuesta, e.titulo, re.fecha_respuesta, re.anonima,
               (SELECT ae.dispositivo
                FROM ACCESO_ENCUESTA ae
                WHERE ae.id_encuesta = re.id_encuesta
                  AND ae.fecha_acceso <= re.fecha_respuesta
                ORDER BY ae.fecha_acceso DESC
                LIMIT 1) AS dispositivo
        FROM RESPUESTA_ENCUESTA re
        JOIN ENCUESTA e ON re.id_encuesta = e.id_encuesta
        ORDER BY re.fecha_respuesta DESC";
$stmt = $pdo->query($sql);
$respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);