<?php
session_start();
require __DIR__ . "/conectar.php";
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_categoria = trim($_POST["nombre_categoria"] ?? "");
    $descripcion      = trim($_POST["descripcion"] ?? "");
    $privado          = isset($_POST["privado"]); // true si tildó "Mantener privado"
    $estado_activo    = $privado ? 0 : 1; // ajustá el sentido según tu regla de negocio

    if ($nombre_categoria == "") {
        $mensaje = "Falta el nombre de la categoría";
    } elseif ($descripcion == "") {
        $mensaje = "Añada una descripción";
    } elseif (!isset($_FILES["logo"]) || $_FILES["logo"]["error"] !== UPLOAD_ERR_OK) {
        $mensaje = "Falta seleccionar una imagen válida";
    } else {
        $archivo = $_FILES["logo"];

        $tiposPermitidos = ["image/jpeg", "image/png"];
        $tipoReal = mime_content_type($archivo["tmp_name"]);

        if (!in_array($tipoReal, $tiposPermitidos)) {
            $mensaje = "El archivo debe ser una imagen (jpg, png o pdf)";
        } else {
            $extension     = pathinfo($archivo["name"], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid("cat_") . "." . $extension;
            $rutaDestino   = __DIR__ . "/Categoria_Logo/" . $nombreArchivo;
            $rutaWeb       = "Categoria_Logo/" . $nombreArchivo;

            if (!move_uploaded_file($archivo["tmp_name"], $rutaDestino)) {
                $mensaje = "Error al guardar la imagen";
            } else {
                try {
                    $sql = "INSERT INTO categoria_documento (nombre_categoria, descripcion, estado_activo, archivo_url) 
                            VALUES (:nombre_categoria, :descripcion, :estado_activo, :archivo_url)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        "nombre_categoria" => $nombre_categoria,
                        "descripcion"      => $descripcion,
                        "estado_activo"    => $estado_activo,
                        "archivo_url"      => $rutaWeb
                    ]);
                    $mensaje = "Categoría registrada correctamente";
                } catch (PDOException $e) {
                    if ($e->getCode() == "23000") {
                        $mensaje = "Ya existe una categoría con ese nombre";
                    } else {
                        $mensaje = "Error al registrar la categoría" . $e->getMessage();
                    }
                }
            }
        }
    }

    $_SESSION["mensaje_cat_doc"] = $mensaje;
}

header("Location: Panel_Admin.php");
exit;