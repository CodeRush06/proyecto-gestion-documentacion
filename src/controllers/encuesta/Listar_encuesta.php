<?php
require __DIR__ . '../config/database.php'; // la variable __DIR__ devuelve la ruta absoluta del archivo presente. La usamos para no tener problemas ya que database.php se ubica en otro directorio
$sql = "SELECT * FROM ENCUESTA";
$stmt = $pdo->query($sql);
$encuestas = $stmt->fetchAll(); // ahora $encuestas es un array de todas las encuestas. cada fila es una encuesta junto a sus atributos. Es un array asociativo (diccionario en phyton)
?>
