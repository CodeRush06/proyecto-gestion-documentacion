<?php
require __DIR__ . '../config/database.php'; // la variable __DIR__ devuelve la ruta absoluta del archivo presente. La usamos para no tener problemas ya que database.php se ubica en otro directorio
$sql = "SELECT * FROM DOCUMENTO";
$stmt = $pdo->query($sql);
$documentos = $stmt->fetchAll(); // ahora $documentos es un array de todos los documentos. cada fila es un documento junto a sus atributos. Es un array asociativo (diccionario en phyton)
?>
