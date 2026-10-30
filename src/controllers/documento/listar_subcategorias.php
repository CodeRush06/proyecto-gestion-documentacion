<?php
require  __DIR__ .'/../../config/database.php';

$recibir_subcategoria = "SELECT
            sc.id_subcategoria,
            sc.nombre_subcategoria,
            sc.descripcion,
            sc.estado_activo,
            sc.id_categoria,
            cd.nombre_categoria
        FROM SUBCATEGORIA_DOCUMENTO sc
        JOIN CATEGORIA_DOCUMENTO cd ON sc.id_categoria = cd.id_categoria";
$stmt = $pdo->query($recibir_subcategoria);
