<?php
session_start();
require "conectar.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_tipo  = trim($_POST["nombre_tipo"] ?? "");
    $descripcion  = trim($_POST["descripcion"] ?? "");
    $privado      = isset($_POST["privado"]) ? 1 : 0;
    $estado_activo = $privado ? 0 : 1; // "Mantener privado" = no visible = desactivado

    if ($nombre_tipo == "") {
        $mensaje = "Falta el nombre";
    } elseif ($descripcion == "") {
        $mensaje = "Añada una descripcion";
    } else {
        try {
            $sql  = "INSERT INTO TIPO_ENCUESTA (nombre_tipo, descripcion, estado_activo) 
                    VALUES (:nombre_tipo, :descripcion, :estado_activo)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                "nombre_tipo"   => $nombre_tipo,
                "descripcion"   => $descripcion,
                "estado_activo" => $estado_activo
            ]);
            $mensaje = "Categoria de encuesta registrada correctamente";
        } catch (PDOException $e) {
            // 23000 = viola restricción (nombre_tipo repetido, si tenés UNIQUE)
            if ($e->getCode() == "23000") {
                $mensaje = "Ese nombre de tipo de encuesta ya existe";
            } else {
                $mensaje = "Error al registrar";
            }
        }
    }
    $_SESSION["mensaje_cat_enc"] = $mensaje;
}
header("Location: Panel_Admin.php");
exit;