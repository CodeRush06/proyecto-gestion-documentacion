<?php
require __DIR__ . "/conectar.php";

$recibir_titulos = "SELECT d.*, 
                            cd.nombre_categoria AS categoria_directa,
                            sd.nombre_subcategoria,
                            cd2.nombre_categoria AS categoria_de_subcategoria
                     FROM DOCUMENTO d
                     LEFT JOIN CATEGORIA_DOCUMENTO cd ON d.id_categoria = cd.id_categoria
                     LEFT JOIN SUBCATEGORIA_DOCUMENTO sd ON d.id_subcategoria = sd.id_subcategoria
                     LEFT JOIN CATEGORIA_DOCUMENTO cd2 ON sd.id_categoria = cd2.id_categoria";
$stmt = $pdo->query($recibir_titulos);

$titulos = $stmt->fetchAll(PDO::FETCH_ASSOC);